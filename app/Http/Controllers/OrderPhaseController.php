<?php
namespace App\Http\Controllers;

use App\Models\OrderPhase;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class OrderPhaseController extends Controller
{
    public function index()
    {
        return response()->json(OrderPhase::all(), 200);
    }

    public function show($id)
    {
        $orderPhase = OrderPhase::find($id);
        if (!$orderPhase) {
            return response()->json(['message' => 'Order Phase not found'], 404);
        }
        return response()->json($orderPhase, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'phase_id' => 'required|integer',
            'service_id' => 'required|integer',
            'order_id' => 'required|integer',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png',
            'audios.*' => 'nullable|file',
            'videos.*' => 'nullable|file',
            'description' => 'nullable|string',
        ]);

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('phase/images'), $imageName);
                $images[] = 'phase/images/' . $imageName;
            }
        }

        // Handle audio uploads
        $audios = [];
        if ($request->hasFile('audios')) {
            foreach ($request->file('audios') as $audio) {
                $audioName = time() . '_' . $audio->getClientOriginalName();
                $audio->move(public_path('phase/audios'), $audioName);
                $audios[] = 'phase/audios/' . $audioName;
            }
        }

        // Handle video uploads
        $videos = [];
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $videoName = time() . '_' . $video->getClientOriginalName();
                $video->move(public_path('phase/videos'), $videoName);
                $videos[] = 'phase/videos/' . $videoName;
            }
        }

        // Create the order phase
        $orderPhase = OrderPhase::create([
            'phase_id' => $validatedData['phase_id'],
            'service_id' => $validatedData['service_id'],
            'order_id' => $validatedData['order_id'],
            'workshop_user_id' => auth()->user()->id,
            'images' => $images,
            'audios' => $audios,
            'videos' => $videos,
            'description' => $validatedData['description'] ?? null,
        ]);
        $data = [
            'user_id' => auth()->user()->id,
            'text_en' => "Your phase request submit successfully.",
            'text_ar' => "تم تقديم طلب المرحلة بنجاح.",
            'request_id' => $validatedData['order_id'],
            'page' => $request->page
        ];
        storeNotification($data);

        
        return response()->json($orderPhase, 200);
    }


    public function update(Request $request, $id)
    {
        $orderPhase = OrderPhase::find($id);
        if (!$orderPhase) {
            return response()->json(['message' => 'Order Phase not found'], 404);
        }

        $validatedData = $request->validate([
            'phase_id' => 'sometimes|integer',
            'service_id' => 'sometimes|integer',
            'order_id' => 'sometimes|integer',
            'workshop_user_id' => 'sometimes|integer',
            'images' => 'nullable|array',
            'audios' => 'nullable|array',
            'videos' => 'nullable|array',
            'description' => 'nullable|string',
        ]);
        
        
        $orderPhase->update($validatedData);
        $data = [
            'user_id' => auth()->user()->id,
            'text_en' => "Your order phase updated successfully",
            'text_ar' => "تم تحديث مرحلة طلبك بنجاح.",
            'request_id' => $validatedData['order_id'],
            'page' => $request->page
        ];
        storeNotification($data);
        return response()->json($orderPhase, 200);
    }

    public function destroy($id)
    {
        $orderPhase = OrderPhase::find($id);
        if (!$orderPhase) {
            return response()->json(['message' => 'Order Phase not found'], 404);
        }

        $orderPhase->delete();
        return response()->json(['message' => 'Order Phase deleted successfully'], 200);
    }
    public function updateStatus(Request $request, $id)
    {
        $orderPhase = OrderPhase::find($id);
        if ($orderPhase) {
            $orderPhase->status = $request->status; // Assuming there's a 'status' column
            $orderPhase->save();
            $data = [
                'user_id' => $orderPhase->workshop_user_id,
                'text_en' => "Your order phase status changed (".($request->status == 0 ? "Unapproved" : "Approved").") successfully",
                'text_ar' => "تم تغيير حالة مرحلة طلبك إلى ". $request->status == 0 ? 'غير معتمد' : 'معتمد' ." بنجاح.",
                'request_id' => $orderPhase->order_id,
                'page' => $request->page
            ];
            storeNotification($data);
            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'OrderPhase not found.'], 404);
    }
}
