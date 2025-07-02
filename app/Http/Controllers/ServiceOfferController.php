<?php
namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\ServiceOffer;
use Illuminate\Support\Facades\Storage;

class ServiceOfferController extends Controller
{
    public function index()
    {
        $serviceOffers = ServiceOffer::with('service')->get(); // Fetch offers with related service data
        return view('service_offers.index', compact('serviceOffers'));
    }


    public function create()
    {
        $services = Service::all(); // Fetch all services from the database
        return view('service_offers.create', compact('services')); // Pass data to the view
    }
    

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_id' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $image = $request->file('image');
        $imagePath = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('images'), $imagePath);

        ServiceOffer::create([
            'service_id' => $validatedData['service_id'],
            'image' => $imagePath,
            'discount' => $validatedData['discount'],
            'status' => $validatedData['status'],
        ]);

        return redirect()->route('service_offers.index')->with('success', 'Service Offer added successfully!');
    }

    public function edit(ServiceOffer $serviceOffer)
    {
        // Retrieve all services to populate the dropdown
        $services = Service::all();

        // Pass the services and the current service offer to the view
        return view('service_offers.edit', compact('serviceOffer', 'services'));
    }


    public function update(Request $request, ServiceOffer $serviceOffer)
    {
        $validatedData = $request->validate([
            'service_id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($serviceOffer->image && file_exists(public_path('images/'.$serviceOffer->image))) {
                unlink(public_path('images/'.$serviceOffer->image));
            }
            // Upload new image
            $image = $request->file('image');
            $imagePath = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imagePath);
            $serviceOffer->image = $imagePath;
        }

        $serviceOffer->update([
            'service_id' => $validatedData['service_id'],
            'image' => $serviceOffer->image, // Keep old image if not updated
            'discount' => $validatedData['discount'],
            'status' => $validatedData['status'],
        ]);

        return redirect()->route('service_offers.index')->with('success', 'Service Offer updated successfully!');
    }

    public function destroy(ServiceOffer $serviceOffer)
    {
        if($serviceOffer->image && file_exists(public_path('images/' . $serviceOffer->image))){
            // Delete old image
            unlink(public_path('images/' . $serviceOffer->image));

        }
        $serviceOffer->delete();

        return redirect()->route('service_offers.index')->with('success', 'Service Offer deleted successfully!');
    }
    public function deleteImage($id)
    {
        $serviceOffer = ServiceOffer::findOrFail($id);
        // print_r($serviceOffer->image); exit;
        if ($serviceOffer->image) {
            if($serviceOffer->image && file_exists(public_path('images/' . $serviceOffer->image))){
                // Delete old image
                unlink(public_path('images/' . $serviceOffer->image));

            }
            $serviceOffer->update(['image' => '']);

            return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
            
        }
        
        return response()->json(['success' => false, 'message' => 'Failed.']);

    }

    

}
