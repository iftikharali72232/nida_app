<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceOrder;

class ServiceController extends Controller
{
    // Get all services
    public function index()
    {
        $services = Service::with('category')->get();
        return response()->json($services);
    }
    public function latestServices()
    {
        $services = Service::with('category')
                    ->latest() // Orders by created_at DESC
                    ->take(10) // Limits to 10 records
                    ->get();
    
        return response()->json($services);
    }
    public function offerDetail($id)
    {
        $serviceOffers = ServiceOffer::with('service')->where('id', $id)->first(); // Fetch offers with related service data
        $serviceOffers->image_path = url('/images/'.$serviceOffers->image);
        return response()->json($serviceOffers);
    }
    // Show a single service
    public function show($id)
    {
        $service = Service::with(['category', 'servicePhases'])->find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }
        $service->offer = ServiceOffer::where('service_id', $id)->where('status', 1)->first();
        $service->thumbnail_base_url = public_path('thumbnails');
        $service->images_base_url = public_path('images');
        return response()->json($service);
    }

    // Store a new service
    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'estimated_time' => 'required',
            'start_time' => 'required',
            'service_cost' => 'required|numeric|min:0',
            'actual_cost' => 'required|numeric|min:0',
            'service_phases' => 'nullable|array',
        ]);

        // Handle Thumbnail Upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // Handle Multiple Images Upload
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = $imageName;
            }
        }

        // Create Service
        $service = Service::create([
            'service_name' => $request->service_name,
            'description' => $request->description,
            'thumbnail' => $thumbnailPath,
            'images' => json_encode($imagePaths),
            'category_id' => $request->category_id,
            'estimated_time' => $request->estimated_time,
            'start_time' => $request->start_time,
            'service_cost' => $request->service_cost,
            'actual_cost' => $request->actual_cost,
        ]);

        // Create Service Phases
        if ($request->has('service_phases')) {
            foreach ($request->input('service_phases') as $phase) {
                $service->servicePhases()->create(['phase' => $phase]);
            }
        }

        return response()->json($service, 201);
    }

    // Update an existing service
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'estimated_time' => 'required',
            'start_time' => 'required',
            'service_cost' => 'required|numeric|min:0',
            'actual_cost' => 'required|numeric|min:0',
            'service_phases' => 'nullable|array',
        ]);

        // Handle Thumbnail Upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $service->thumbnail = $thumbnailPath;
        }

        // Handle Multiple Images Upload
        $imagePaths = json_decode($service->images, true) ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('images', 'public');
            }
        }

        // Update Service
        $service->update([
            'service_name' => $request->service_name,
            'description' => $request->description,
            'images' => json_encode($imagePaths),
            'category_id' => $request->category_id,
            'estimated_time' => $request->estimated_time,
            'start_time' => $request->start_time,
            'service_cost' => $request->service_cost,
            'actual_cost' => $request->actual_cost,
        ]);

        // Update Service Phases
        $service->servicePhases()->delete();
        if ($request->has('service_phases')) {
            foreach ($request->input('service_phases') as $phase) {
                $service->servicePhases()->create(['phase' => $phase]);
            }
        }

        return response()->json($service);
    }

    // Delete a service
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        // Delete the thumbnail if it exists
        if ($service->thumbnail && file_exists(public_path('thumbnails/' . $service->thumbnail))) {
            unlink(public_path('thumbnails/' . $service->thumbnail));
        }

        // Delete the images if they exist
        if ($service->images) {
            $images = json_decode($service->images);
            foreach ($images as $image) {
                $imagePath = public_path('images/' . $image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }

        // Delete the service record
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
    function recentServices()
    {
        $user = auth()->user();
        if($user->user_type == 1){
            $serviceIds = ServiceOrder::where('customer_id', $user->id)->pluck('service_id');
            $serviceIds = array_unique(json_decode(json_encode($serviceIds), true));
    
            $services = Service::with(['category', 'servicePhases'])
                ->whereIn('id', $serviceIds)
                ->get();

        } else if($user->user_type == 2) {
            $serviceIds = ServiceOrder::where('team_user_id', $user->id)->pluck('service_id');
            $serviceIds = array_unique(json_decode(json_encode($serviceIds), true));
    
            $services = Service::with(['category', 'servicePhases'])
                ->whereIn('id', $serviceIds)
                ->get();
        }

        return response()->json($services);
    }
    public function offerList()
    {
        $serviceOffers = ServiceOffer::where('status', 1)->get();

        // Prepare the response as an associative array to avoid numeric indexes
        $response = [
            'offers' => $serviceOffers,
            'image_base_url' => public_path('images'),
        ];

        return response()->json($response);
    }
}
