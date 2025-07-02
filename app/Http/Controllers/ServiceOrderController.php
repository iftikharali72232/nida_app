<?php
namespace App\Http\Controllers;

use App\Models\OrderPhase;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceOrder;
use App\Models\ServicePhase;
use App\Models\Team;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceOrder::join('users', 'service_orders.customer_id', '=', 'users.id')
            ->join('services', 'service_orders.service_id', '=', 'services.id')
            ->where('users.user_type', 1)
            ->select('service_orders.*', 'users.name as customer_name', 'services.service_name as service_name');

        // If a status filter is applied, add a where clause
        if ($request->filled('status')) {
            $query->where('service_orders.status', $request->status);
        }

        // Apply pagination (e.g., 10 items per page)
        $serviceOrders = $query->orderBy('id', 'desc')->paginate(10);

        return view('service_orders.index', compact('serviceOrders'));
    }

    // Show the service order creation form
    public function create(Request $request)
    {
        $services = Service::all(); // Fetch all services
        $users = User::where('user_type', 1)->where('status', 1)->pluck('id', 'name');
        $serviceData = null; // Default to null
        $variables = []; // Default to empty array

        if ($request->has('service_id')) {
            // Fetch service data
            $serviceData = Service::find($request->service_id);

            if ($serviceData && $serviceData->variables_json) {
                // Decode JSON if it exists
                $variables = json_decode($serviceData->variables_json, true);
            }
        }
        // print_r($variables); exit;
        return view('service_orders.create', compact('services', 'serviceData', 'variables', 'users'));
    }
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:service_orders,id',
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
        ]);
    
        $order = ServiceOrder::find($request->order_id);
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        $order->team_id = $request->team_id;
        $order->team_user_id = $request->user_id;
        $order->status = 1;
        $order->save();
    
        return response()->json(['success' => 'Order updated successfully']);
    }
    
    public function show($id)
    {
        $serviceOrder = ServiceOrder::with(['service', 'customer'])
            ->where('id', $id)
            ->firstOrFail();

        // Decode variables_json
        $variables = $serviceOrder->variables_json ? json_decode($serviceOrder->variables_json, true) : [];

        // Fetch service phases
        $phases = ServicePhase::where('service_id', $serviceOrder->service_id)
        ->get();
        foreach($phases as $pk => $phase)
        {
            $phases[$pk]['response'] = OrderPhase::where('order_id', $id)->where('phase_id', $phase->id)->first();
        }
        // Check for an active offer
        $activeOffer = ServiceOffer::where('service_id', $serviceOrder->service_id)
            ->where('status', 1)
            ->first();
        $service = Service::where('id', $serviceOrder->service_id)->first();
        $teams = Team::where('category_id', $service->category_id)->get();
        $users = $serviceOrder->team_id > 0 ? User::where('team_id', $serviceOrder->team_id)->get() : [];
        // echo "<pre>";print_r($phases); exit;
        return view('service_orders.show', compact('serviceOrder', 'variables', 'activeOffer', 'phases', 'teams', 'users'));
    }

    public function getTeamUsers($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json(['error' => 'Team not found'], 404);
        }

        $users = User::where('team_id', $id)->get(); // Assuming a one-to-many relationship exists between Team and User
        return response()->json($users);
    }



    // Fetch service data based on selected service
    public function fetchServiceData(Request $request)
    {
        $serviceId = $request->input('service_id');
        $service = Service::find($serviceId);

        if ($service) {
            return response()->json($service);
        }

        return response()->json(['error' => 'Service not found'], 404);
    }

    // Store the service order in the database

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_id'   => 'required|integer|exists:services,id',
            'variables'    => 'required|array',
            'service_cost' => 'required|numeric',
            'tax'          => 'nullable|string',    // raw input, can be "10%" or numeric string
            'discount'     => 'nullable|string',    // raw input, can be "5%" or numeric string
            'service_date' => 'required|date',
            'customer_id'  => 'required|int',
        ]);
    
        $user = User::find($request->customer_id);
        if (empty($user)) {
            return redirect()->back()->withErrors(['voucher' => "User does not exist"]);
        }
    
        $wallet = Wallet::where('user_id', $request->customer_id)->first();
        if (empty($wallet)) {
            return redirect()->back()->withErrors(['voucher' => "Your Voucher ID is invalid"]);
        }
    
        $baseCost = $validatedData['service_cost'];
    
        // Calculate tax amount for final cost deduction
        $calcTax = 0;
        if (!empty($validatedData['tax'])) {
            if (strpos($validatedData['tax'], '%') !== false) {
                $taxPercentage = floatval(str_replace('%', '', $validatedData['tax']));
                $calcTax = ($baseCost * $taxPercentage) / 100;
            } else {
                $calcTax = floatval($validatedData['tax']);
            }
        }
    
        // Calculate discount amount for final cost deduction
        $calcDiscount = 0;
        if (!empty($validatedData['discount'])) {
            if (strpos($validatedData['discount'], '%') !== false) {
                $discountPercentage = floatval(str_replace('%', '', $validatedData['discount']));
                $calcDiscount = ($baseCost * $discountPercentage) / 100;
            } else {
                $calcDiscount = floatval($validatedData['discount']);
            }
        }
    
        // Final cost calculation: base cost + calculated tax - calculated discount
        $finalCost = $baseCost + $calcTax - $calcDiscount;
    
        // if ($finalCost > $wallet->amount) {
        //     return redirect()->back()->withErrors(['voucher' => "Your Voucher ID does not have enough points to get this service"]);
        // }
    
        $serviceData = Service::find($request->service_id);
        $variables = [];
        foreach ($request->variables as $label => $value) {
            $variable = collect(json_decode($serviceData->variables_json, true))
                ->firstWhere('label', $label);
    
            $variables[] = [
                'label'           => $label,
                'type'            => $variable['type'] ?? '',
                'dropdown_values' => $variable['dropdown_values'] ?? null,
                'value'           => $value,
            ];
        }
        $validatedData['variables_json'] = json_encode($variables);
    
        // Wrap operations in a transaction
        DB::transaction(function() use ($validatedData, $wallet, $finalCost, $calcTax, $calcDiscount) {
            // Create Wallet History using the final calculated cost
            WalletHistory::create([
                'wallet_id'   => $wallet->id,
                'amount'      => $finalCost,
                'is_expanse'  => 1,
                'service_id'  => $validatedData['service_id'],
                'description' => 'Charge against Service request (' . $validatedData['service_id'] . ') with Points ' . $finalCost
            ]);
    
            // Deduct final cost from wallet
            $newAmount = $wallet->amount - $finalCost;
            Wallet::where('id', $wallet->id)->update(['amount' => $newAmount]);
    
            // Add the calculated tax and discount amounts to the validated data
            $validatedData['tax_amount'] = $calcTax;
            $validatedData['discount_amount'] = $calcDiscount;
            $validatedData['service_cost'] = $finalCost;
            // Create Service Order (storing raw tax/discount in tax and discount columns)
            ServiceOrder::create($validatedData);
        });
    
        return redirect()->route('service_orders.index')->with('success', 'Order created successfully.');
    }
    




    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2,3,4',
        ]);
    
        $serviceOrder = ServiceOrder::findOrFail($id);
    
        DB::transaction(function() use ($request, $serviceOrder) {
            // If status is 3, refund the service cost back to the user's wallet.
            if ($request->status == 3) {
                // Assuming customer_id on ServiceOrder refers to the user id, not wallet id
                // Adjust accordingly if your wallet is identified differently.
                $wallet = Wallet::where('user_id', $serviceOrder->customer_id)->first();
                if ($wallet) {
                    $newAmount = $wallet->amount + $serviceOrder->service_cost;
                    Wallet::where('id', $wallet->id)->update(['amount' => $newAmount]);
                    WalletHistory::create([
                        'wallet_id' => $wallet->id,
                        'amount' => $serviceOrder->service_cost,
                        'is_deposite' => 1,
                        'service_id' => $serviceOrder->service_id,
                        'description' => "Your Points return agains your Cancel Order. (Order ID=".$serviceOrder->id.")",
                    ]);
                }
            }
    
            // Update order status
            $serviceOrder->status = $request->status;
            $serviceOrder->save();
            
            
            $user = User::find($serviceOrder->user_id);
            if($user)
            {
                $data = [
                    'user_id' => $serviceOrder->user_id,
                    'text_en' => "Your order phase status marked (".getOrderStatusText($request->status, "en").") successfully",
                    'text_ar' => "تم تغيير حالة مرحلة طلبك إلى (". getOrderStatusText($request->status, "ar") .") بنجاح.",
                    'request_id' => $serviceOrder->id,
                    'page' => $request->page
                ];
                storeNotification($data);
                $datap = [
                    'is_user' => 1,
                    'device_token' => $user->device_token,
                    'title' => 'Order Status Update',
                    'body' => $data['text_en'],
                    'request_id' => $data['request_id']
                ];
                sendNotification($datap);
            }
            
        });
    
        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

}
