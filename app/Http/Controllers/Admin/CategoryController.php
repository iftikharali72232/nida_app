<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Request as ModelsRequest;
use App\Models\Service;
use App\Models\Shop;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    //
    public function create(Request $request){
        // Step 1: Validate input fields
        $attrs = $request->validate([
            "name" => "required|string",
            "name_ar" => "required|string",
            "description" => "nullable|string",
            "description_ar" => "nullable|string",
            "admin_choice" => "nullable|integer",
            "status" => "nullable|integer"
        ]);

        // Step 2: Check for duplicate category name
        $existingCategory = DB::select("SELECT * FROM categories WHERE name = :name", [':name' => $attrs['name']]);
        if (!empty($existingCategory)) {
            return response([
                'status' => "0",
                'message' => "Category name already exists"
            ], 200);
        }

        // Step 3: Handle image upload if exists
        $file_name = "";
        if ($request->hasFile('image')) {
            $file_name = $this->upload($request);  // Ensure this function is implemented
        }

        // Step 4: Create new category
        $category = Category::create([
            "name" => $attrs["name"],
            "name_ar" => $attrs["name_ar"],
            "image" => $file_name,
            "description" => $request->description,
            "description_ar" => $request->description_ar,
            "admin_choice" => $request->admin_choice ?? 0,
            "status" => $request->status ?? 1,
        ]);

        // Step 5: Return success response
            if ($category) {
                if (!empty($file_name)) {
                    $imageUrl = asset('images/' . $file_name);
                    $category['imageUrl'] = $imageUrl;
                }
                return response([
                    "status" => "1",
                    "category" => $category
                ]);
            } else {
                return response([
                    "status" => "0",
                    "message" => "Something went wrong."
                ]);
            }
    }


    public function update(Request $request, $id)
    {
        // Step 1: Find category
        $category = Category::find($id);
        if (!$category) {
            return response([
                'status' => "0",
                'message' => "Category not found"
            ], 404);
        }

        // Step 2: Validate input fields
        $attrs = $request->validate([
            "name" => "required|string",
            "name_ar" => "required|string",
            "description" => "nullable|string",
            "description_ar" => "nullable|string",
            "admin_choice" => "nullable|integer",
            "status" => "nullable|integer"
        ]);

        // Step 3: Check for duplicate category name (exclude current)
        $existingCategory = DB::select(
            "SELECT * FROM categories WHERE name = :name AND id != :id",
            [':name' => $attrs['name'], ':id' => $id]
        );

        if (!empty($existingCategory)) {
            return response([
                'status' => "0",
                'message' => "Category name already exists"
            ], 200);
        }

        // Step 4: Handle image upload if exists
        if ($request->hasFile('image')) {
            // delete old image if exists
            if (!empty($category->image) && file_exists(public_path('images/' . $category->image))) {
                unlink(public_path('images/' . $category->image));
            }

            $file_name = $this->upload($request); // Ensure this function is implemented
            $category->image = $file_name;
        }

        // Step 5: Update fields
        $category->name = $attrs["name"];
        $category->name_ar = $attrs["name_ar"];
        $category->description = $request->description;
        $category->description_ar = $request->description_ar;
        $category->admin_choice = $request->admin_choice ?? 0;
        $category->status = $request->status ?? 1;

        $category->save();

        // Step 6: Return success response
        if (!empty($category->image)) {
            $category['imageUrl'] = asset('images/' . $category->image);
        }

        return response([
            "status" => "1",
            "category" => $category
        ]);
    }

    public function getCategory($id){
        $category = DB::select("SELECT * FROM categories WHERE id=:id",[':id' => $id]);
        if($category)
        {
            $category = json_decode(json_encode($category[0]), true);
            $file_name = $category['image'];
            if(!empty($file_name))
            {
                $imageUrl = asset('images/'.$file_name);
                $category['imageUrl'] = $imageUrl;
            } 
            return response([
                "status"=> "1",
                "category" => $category
            ]);
        } else {
            return response([
                "status"=> "0",
                "message" => "Something went wrong."
            ]);
        }
    }
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);

        return $imageName;
    }

    public function dashboardRequest()
    {
        //fvrt shops
        $fvrtShops = fvrt_shop_list();
        //visted shops
        $vistedShops = last_visited_shop_list();
        //current token
        $currentToken = current_user_token();

        return response([
            'status' => '1',
            'fvrtShops' => $fvrtShops,
            'vistedShops' => $vistedShops,
            'currentToken' => $currentToken,
        ]);
    }
    
    function vistedShops(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|integer|min:1'
        ]);

        return last_visited_shop_list($request->perPage);
    }

    function fvrtShops(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|integer|min:1'
        ]);

        return fvrt_shop_list($request->perPage);
    }

    function shop_details($shop_id)
    {
        return shop_details($shop_id);
    }

    function markShopAsFvrt(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|integer',
        ]);

        return fvrt_shop($request->shop_id); // Ensure this function is implemented
    }
    function removeShopAsFvrt(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|integer',
        ]);

        return un_fvrt_shop($request->shop_id); // Ensure this function is implemented
    }




    public function getAllCategories()
    {
        $categories = DB::select("SELECT * FROM categories WHERE status=1");
        if(count($categories) > 0) 
        {
            // print_r(json_decode(json_encode($categories), true)); exit;
            return response([
                'status'=> '1',
                'categories'=> json_decode(json_encode($categories), true),
                'image_base_url' => asset('images/')
            ]);
        } else {
            return response([
                'status'=> '0',
                'categories'=> []
            ], 404);
        }
    }
    public function delete($id)
    {
        $category = Category::find( $id );
        $shops = DB::select('SELECT id FROM shops WHERE category_id=:cat_id', [':cat_id' => $id]);
        // print_r($category); exit;
        if($category){
            if(count($shops) == 0 && $category->delete())
            {
                return response([
                    'status'=> '1',
                    'message' => "Category Delete successfully"
                ], 200);
            }else if($category) {
                return response([
                    "status"=> "0",
                    "message"=> "You cannot delete this category, This is use in shops."
                ],200);
            }
        } else {
            return response([
                "status"=> "0",
                "message"=> "Category not found"
            ],200);
        }
    }

    public function categories()
    {
        $categories = DB::table('categories')->where("status",1)->get();
        // print_r(json_decode(json_encode($categories), true));
        if(count($categories) > 0)
        {
            return response([
                "status"=> "1",
                "categories"=> json_decode(json_encode($categories), true),
            ],200);
        } else {
            return response([
                "status"=> "0",
                "message"=> "categories not found"
            ],200);
        }
    }

    public function adminChoiceCategories()
    {
        $categories = DB::table("categories")->where("status","=", 1)->where("admin_choice","=", 1)->get();
        return response([
            "status" => 1,
            "categories" => json_decode(json_encode($categories), true)
        ]);
    }

    public function sellerCategories()
    {
        $shop = Shop::where('created_by', auth()->user()->id)->first();
        // print_r($shop); exit;
        $categoryIdsArray = explode(',', $shop->category_id);
        // print_r($categoryIdsArray); exit;
        $categories = Category::whereIn('id', $categoryIdsArray)->get();

        return response([
            "status" => 1,
            "categories" => json_decode(json_encode($categories), true)
        ]);
    }

    function getAllServices(Request $request)
    {
        $request->validate([
            'category_id' => 'required|int'
        ]);

        $services = Service::where('category_id', $request->category_id)->get();

        return response()->json([
            'status' => 1,
            'services' => $services,
            'thumbnail_image_base_url' => asset('thumbnails/'),
            'images_base_url' => asset('images/')
        ]);
    }
}
