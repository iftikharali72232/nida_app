<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Team;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceOrderController extends Controller
{


    public function create(Request $request)
    {
        $data = $request->validate([
            'service_id'      => 'required|integer',
            'variables_json'  => 'required',
            'service_cost'    => 'required|numeric',
            'service_date'    => 'required|date',
            'tax'             => 'nullable|string',    // Accept percentage string (e.g. "10%") or numeric string
            'discount'        => 'nullable|string',    // Accept percentage string (e.g. "5%") or numeric string
            'wallet_id'       => 'required'
        ]);
    
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }
    
        $walletParts = explode('-', base64_decode($request->wallet_id));
        $wallet_id = $walletParts[0];
        $user_id = $walletParts[1];
    
        $wallet = Wallet::where('id', $wallet_id)->where('user_id', $user_id)->first();
        if (empty($wallet)) {
            return response()->json(['msg' => "Your Voucher ID is invalid", 'status' => 0]);
        }
    
        $baseCost = $data['service_cost'];
    
        // Calculate tax amount for deduction
        $calcTax = 0;
        if (!empty($data['tax'])) {
            if (strpos($data['tax'], '%') !== false) {
                $taxPercentage = floatval(str_replace('%', '', $data['tax']));
                $calcTax = ($baseCost * $taxPercentage) / 100;
            } else {
                $calcTax = floatval($data['tax']);
            }
        }
    
        // Calculate discount amount for deduction
        $calcDiscount = 0;
        if (!empty($data['discount'])) {
            if (strpos($data['discount'], '%') !== false) {
                $discountPercentage = floatval(str_replace('%', '', $data['discount']));
                $calcDiscount = ($baseCost * $discountPercentage) / 100;
            } else {
                $calcDiscount = floatval($data['discount']);
            }
        }
    
        // Final cost calculation: base cost + tax - discount
        $finalCost = $baseCost + $calcTax - $calcDiscount;
    
        // if ($finalCost > $wallet->amount) {
        //     return response()->json(['msg' => "Your Voucher ID does not have enough points to get this service", 'status' => 0]);
        // }
    
        // Start transaction
        DB::beginTransaction();
    
        try {
            // Deduct final cost from wallet
            $wallet->amount -= $finalCost;
            $wallet->save();
    
            // Create Wallet History
            WalletHistory::create([
                'wallet_id'   => $wallet->id,
                'amount'      => $finalCost,
                'is_expanse'  => 1,
                'service_id'  => $data['service_id'],
                'description' => 'Charge against Service request (' . $data['service_id'] . ') with Points ' . $finalCost
            ]);
    
            // Set customer_id from authenticated user
            $data['customer_id'] = $user->id;
            // Save calculated tax and discount amounts in new columns
            $data['tax_amount'] = $calcTax;
            $data['discount_amount'] = $calcDiscount;
            $data['service_cost'] = $finalCost;
            // Create Service Order (storing raw tax and discount as provided in tax and discount columns)

            $order = ServiceOrder::create($data);
            $data = [
                'user_id' => auth()->user()->id,
                'text_en' => "You order placed successfully.",
                'text_ar' => "تم تقديم طلبك بنجاح.",
                'request_id' => $order->id,
                'page' => $request->page
            ];
            storeNotification($data);

            // Commit transaction
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Service order created successfully.',
                'data'    => $order,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service order. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    


    public function createOrder(Request $request)
    {
        $serviceId = $request->input('service_id');
        $service = Service::find($serviceId);
        return response()->json($service);
    }

    public function userOrders()
    {
        $user = auth()->user();
        if($user->user_type == 1)
        {
            $orders = ServiceOrder::where('customer_id', $user->id)->get();
        } else {
            $orders = ServiceOrder::where('team_user_id', $user->id)->get();
        }

        $res = [];
        foreach($orders as $order)
        {
            $order['service'] = Service::where('id', $order->service_id)->first();
            $res[] = $order;
        }
        return response()->json($res);
    }
    public function singleOrder($id)
    {
        $order = ServiceOrder::find($id);
        $order['team'] = Team::find($order->team_id);
        $order['team_user'] = User::find($order->team_user_id);
        
        $order['service'] = Service::with(['category',
                                'servicePhases.orderPhases' => function ($query) use ($order) {
                                    $query->where('order_id', $order->id);
                                }
                            ])->find($order->service_id);
        
        return response()->json($order);
    }
    public function cancelOrder($id)
    {
        $order = ServiceOrder::find($id);
        if(!empty($order) && $order->status == 0)
        {
            // Start transaction
            DB::beginTransaction();
        
            try {
                $wallet = Wallet::where('id', $order->customer_id)->first();
                $amount = $wallet->amount + $order->service_cost;
                Wallet::where('id', $wallet->id)->update(['amount' => $amount]);

                WalletHistory::create([
                    'wallet_id' => $wallet->id,
                    'amount' => $order->service_cost,
                    'is_deposite' => 1,
                    'service_id' => $order->service_id,
                    'description' => "Your Points return agains your Cancel Order. (Order ID=".$order->id.")",
                ]);

                $order = ServiceOrder::where('id', $id)->update(['status' => 3]);

                $data = [
                    'user_id' => auth()->user()->id,
                    'text_en' => "You order cancel successfully.",
                    'text_ar' => "تم إلغاء طلبك بنجاح.",
                    'request_id' => $id,
                    'page' => ""
                ];
                storeNotification($data);
                // Commit transaction
                DB::commit();
                return response()->json(['msg' => 'Order cancel successfully', 'status' => 1]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel order.',
                    'error'   => $e->getMessage(),
                ], 500);
            }
        } elseif($order->status == 1){
            
            return response()->json(['msg' => 'You can never cancel order now, because the order in processing']);
        }
    }
    public function updateOrderDate(Request $request, $id)
    {
        $request->validate([
            'date' => 'required', // Ensure a valid date is provided
        ]);

        $order = ServiceOrder::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $order->service_date = $request->input('date');
        $order->save();

        $data = [
            'user_id' => auth()->user()->id,
            'text_en' => "Your Order ($id) date change successfully.",
            'text_ar' => "تم تغيير تاريخ طلبك ($id) بنجاح.",
            'request_id' => $id,
            'page' => $request->page
        ];
        storeNotification($data);
        return response()->json([
            'success' => true,
            'message' => 'Order date updated successfully',
            'order' => $order,
        ]);
    }
}
