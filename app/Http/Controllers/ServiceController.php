<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use App\Models\ServicePhase;
use App\Models\ServiceVariable;
use App\Models\ServiceVeriable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    // Display the listing page
    public function index()
    {
        $services = Service::with('category')->paginate(10); // Eager load category relationship
        return view('services.index', compact('services'));
    }

    // Display the form for creating a new service
    public function create()
    {
        $categories = Category::all(); // Fetch all categories
        return view('services.create', compact('categories')); // Pass to the view
    }


    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'estimated_time' => 'required',
            'start_time' => 'required',
            'service_cost' => 'required',
            'actual_cost' => 'required|numeric|min:0',
            'service_variables' => 'nullable|array',
            'service_phases' => 'nullable|array',
        ]);
        
        DB::beginTransaction();

        try {
            $thumbnailPath = "";
            $imagePaths = [];
            
            // Handle Thumbnail Upload
            if ($request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $thumbnailPath = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('thumbnails'), $thumbnailPath);
            }

            // Handle Multiple Images Upload
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images'), $imageName);
                    $imagePaths[] = $imageName;
                }
            }
            $variablesJson = [];
            if ($request->has('service_variables')) {
                foreach ($request->input('service_variables') as $variable) {
                    $variablesJson[] = [
                        'label' => $variable['label'] ?? null,
                        'type' => $variable['type'] ?? null,
                        'dropdown_values' => isset($variable['dropdown_values'])
                            ? explode(',', $variable['dropdown_values'])
                            : null,
                    ];
                }
            }
            // echo "<pre>";print_r($variablesJson); exit;
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
                'variables_json' => json_encode($variablesJson),
            ]);

            if ($request->has('service_phases')) {
                foreach ($request->service_phases as $phase) {
                    if (!empty($phase)) {
                        ServicePhase::create([
                            'service_id' => $service->id,
                            'phase' => $phase,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('services.index')->with('success', 'Service created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create service: ' . $e->getMessage());
        }
    }



    

    public function edit($id)
    {
        $service = Service::with(['servicePhases'])->findOrFail($id);
        $categories = Category::all();
        // dd(compact('service', 'categories'));
        return view('services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'estimated_time' => 'required',
            'start_time' => 'required',
            'service_cost' => 'required',
            'actual_cost' => 'required|numeric|min:0',
            'service_variables' => 'nullable|array',
            'service_phases' => 'nullable|array',
        ]);
    
        $service = Service::findOrFail($id);
    
        // Handle Thumbnail Upload
        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $thumbnailPath = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('thumbnails'), $thumbnailPath);
    
            // Delete old thumbnail if it exists
            if ($service->thumbnail && file_exists(public_path('thumbnails/' . $service->thumbnail))) {
                unlink(public_path('thumbnails/' . $service->thumbnail));
            }
    
            $service->thumbnail = $thumbnailPath;
        }
    
        // Handle Multiple Images Upload
        $imagePaths = json_decode($service->images, true) ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = $imageName;
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
            'variables_json' => json_encode($request->service_variables), // Save variables as JSON
        ]);
    
        // Update Service Phases
        ServicePhase::where('service_id', $service->id)->delete();
        if ($request->has('service_phases')) {
            foreach ($request->input('service_phases') as $phase) {
                if (!empty($phase)) {
                    ServicePhase::create([
                        'service_id' => $service->id,
                        'phase' => $phase,
                    ]);
                }
            }
        }
    
        return redirect()->route('services.index')->with('success', 'Service updated successfully!');
    }
    
    public function show($id)
    {
        // Fetch the service along with its category and phases
        $service = Service::with(['category', 'servicePhases'])->findOrFail($id);

        // Pass the service data to the view
        return view('services.show', compact('service'));
    }


    // Delete the service
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

        return redirect()->route('services.index')->with('success', 'Service deleted successfully!');
    }

    public function deleteThumbnail(Service $service)
    {
        // $service = Service::find($id);
        if ($service->thumbnail && file_exists(public_path('thumbnails/' . $service->thumbnail))) {
            unlink(public_path('thumbnails/' . $service->thumbnail));
            $service->update(['thumbnail' => null]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Thumbnail not found.']);
    }

    public function deleteImage(Service $service, $image)
    {
        $images = json_decode($service->images, true);
        if (in_array($image, $images)) {
            // Remove image from storage
            if (file_exists(public_path('images/' . $image))) {
                unlink(public_path('images/' . $image));
            }

            // Update images array in database
            $images = array_filter($images, fn($img) => $img !== $image);
            $service->update(['images' => json_encode(array_values($images))]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Image not found.']);
    }
}
