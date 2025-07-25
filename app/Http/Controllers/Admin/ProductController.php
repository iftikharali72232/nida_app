<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Shop;


class ProductController extends Controller
{
    //
    public function createProduct(Request $request)
    {
        $attrs = $request->validate([
            "p_name"         => "required|string|max:255",
            "p_name_ar"      => "nullable|string|max:255",
            "category_id"    => "required|integer",
            "price"          => "required|numeric",
            "tax"            => "nullable|string", // because it might be like "10%"
            "discount"       => "nullable|string", // same
            "taxable"        => "nullable|integer|in:0,1",
            "tax_inclusive"  => "nullable|integer|in:0,1",
            "status"         => "nullable|integer|in:0,1",
            "description"    => "nullable|string",
            "description_ar" => "nullable|string",
            "extra_options"  => "nullable|string",
            "images.*"       => "nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048"
        ]);

        $price = round($attrs["price"], 2);

        // Tax
        $tax_value = 0;
        if (!empty($request->tax)) {
            if (strpos($request->tax, "%") !== false) {
                $tax_percent = doubleval(str_replace("%", "", $request->tax));
                $tax_value   = round(($price * $tax_percent) / 100, 2);
            } else {
                $tax_value = doubleval($request->tax);
            }
        }

        // Discount
        $discount_value = 0;
        if (!empty($request->discount)) {
            if (strpos($request->discount, "%") !== false) {
                $discount_percent = doubleval(str_replace("%", "", $request->discount));
                $discount_value   = round(($price * $discount_percent) / 100, 2);
            } else {
                $discount_value = doubleval($request->discount);
            }
        }

        // Check if product name already exists
        if (Product::where('p_name', $attrs['p_name'])->exists()) {
            return response([
                'status'  => "0",
                'message' => "Product already exists"
            ], 409);
        }

        // Check category
        $category = DB::table('categories')->where([
            ['id', '=', $attrs['category_id']],
            ['status', '=', 1]
        ])->first();

        if (!$category) {
            return response([
                'status'  => "0",
                'message' => "Category not found or inactive"
            ], 404);
        }


        // Upload Images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $images[] = $imageName;
            }
        }

        $product = Product::create([
            "p_name"         => $attrs["p_name"],
            "p_name_ar"      => $attrs["p_name_ar"],
            "price"          => $price,
            "tax"            => $tax_value,
            "discount"       => $discount_value,
            "taxable"        => $attrs["taxable"] ?? 0,
            "tax_inclusive"  => $attrs["tax_inclusive"] ?? 0,
            "category_id"    => $attrs['category_id'],
            "shop_id"        => Auth::user()->id,
            "images"         => json_encode($images),
            "status"         => $attrs["status"] ?? 1,
            "description"    => $attrs["description"] ?? null,
            "description_ar" => $attrs["description_ar"] ?? null,
            "extra_options"  => $attrs["extra_options"] ?? null
        ]);

        if ($product) {
            $product['image_base_url'] = asset("images/");
            return response([
                "status"  => "1",
                "product" => $product
            ], 201);
        } else {
            return response([
                "status"  => "0",
                "message" => "Something went wrong"
            ], 500);
        }
    }


    public function updateProduct(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response([
                'status'  => "0",
                'message' => "Product not found"
            ], 404);
        }

        $attrs = $request->validate([
            "p_name"         => "sometimes|required|string|max:255",
            "p_name_ar"      => "nullable|string|max:255",
            "category_id"    => "sometimes|required|integer",
            "price"          => "sometimes|required|numeric",
            "tax"            => "nullable|string",
            "discount"       => "nullable|string",
            "taxable"        => "nullable|integer|in:0,1",
            "tax_inclusive"  => "nullable|integer|in:0,1",
            "status"         => "nullable|integer|in:0,1",
            "description"    => "nullable|string",
            "description_ar" => "nullable|string",
            "extra_options"  => "nullable|string",
            "images.*"       => "nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048"
        ]);

        if (isset($attrs['p_name'])) {
            if (Product::where('p_name', $attrs['p_name'])->where('id', '!=', $id)->exists()) {
                return response([
                    'status'  => "0",
                    'message' => "Another product with this name already exists"
                ], 409);
            }
            $product->p_name = $attrs['p_name'];
        }

        if (isset($attrs['p_name_ar']))       $product->p_name_ar = $attrs['p_name_ar'];
        if (isset($attrs['category_id'])) {
            $category = DB::table('categories')->where([
                ['id', '=', $attrs['category_id']],
                ['status', '=', 1]
            ])->first();

            if (!$category) {
                return response([
                    'status'  => "0",
                    'message' => "Category not found or inactive"
                ], 404);
            }
            $product->category_id = $attrs['category_id'];
        }

        if (isset($attrs['price'])) $product->price = round($attrs['price'], 2);

        // Tax
        if ($request->has('tax')) {
            $price = $product->price;
            $tax_value = 0;
            if (strpos($request->tax, "%") !== false) {
                $tax_percent = doubleval(str_replace("%", "", $request->tax));
                $tax_value   = round(($price * $tax_percent) / 100, 2);
            } else {
                $tax_value = doubleval($request->tax);
            }
            $product->tax = $tax_value;
        }

        // Discount
        if ($request->has('discount')) {
            $price = $product->price;
            $discount_value = 0;
            if (strpos($request->discount, "%") !== false) {
                $discount_percent = doubleval(str_replace("%", "", $request->discount));
                $discount_value   = round(($price * $discount_percent) / 100, 2);
            } else {
                $discount_value = doubleval($request->discount);
            }
            $product->discount = $discount_value;
        }

        if (isset($attrs['taxable']))        $product->taxable = $attrs['taxable'];
        if (isset($attrs['tax_inclusive'])) $product->tax_inclusive = $attrs['tax_inclusive'];
        if (isset($attrs['status']))        $product->status = $attrs['status'];
        if (isset($attrs['description']))   $product->description = $attrs['description'];
        if (isset($attrs['description_ar']))$product->description_ar = $attrs['description_ar'];
        if (isset($attrs['extra_options'])) $product->extra_options = $attrs['extra_options'];

        // Handle Images
        if ($request->hasFile('images')) {
            // If new images uploaded, delete old images from folder
            $oldImages = json_decode($product->images, true) ?? [];
            foreach ($oldImages as $img) {
                $oldPath = public_path('images/' . $img);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Save new images
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $images[] = $imageName;
            }
            $product->images = json_encode($images);
        } elseif ($request->has('images') && $request->images === null) {
            // If explicitly sent `images = null` then DO NOT change the existing images
            // (so leave $product->images as is)
        }

        $product->updated_at = now();

        if ($product->save()) {
            $product['image_base_url'] = asset("images/");
            return response([
                "status"  => "1",
                "message" => "Product updated successfully",
                "product" => $product
            ], 200);
        } else {
            return response([
                "status"  => "0",
                "message" => "Something went wrong"
            ], 500);
        }
    }


    public function getProduct($id)
    {
        $product = DB::select("SELECT * FROM products WHERE id=:id", [':id' => $id]);
        if($product)
        {
            $is_like = DB::table('wish_lists')->where('product_id', $product[0]->id)->get();
            $product[0]->is_like = 0;
            if(isset($is_like[0]->id))
            {
                $product[0]->is_like = 1;
            }
            return response([
                'status'=> '1',
                'product'=> json_decode(json_encode($product[0]), true),
                'image_base_url' => asset('images/')
            ],200);
        } else {
            return response([
                'status'=> '0',
                'message'=> 'Product not found.',
            ], 200);
        }
    }

    // Products List by shop ID
    public function productList($id){
        $shop = DB::select('SELECT * FROM shops WHERE id=:id AND status= 1', [':id'=> $id]);
        if($shop)
        {
            if(isset($shop[0]->category_id) && $shop[0]->category_id > 0)
            {
                $product = DB::select("SELECT * FROM products WHERE status=1 AND created_by =".$shop[0]->created_by);
                if(count($product) > 0){
                    foreach($product as $key => $item){
                        $is_like = DB::table('wish_lists')->where('product_id', $item->id)->get();
                        $product[$key]->is_like = 0;
                        if(isset($is_like[0]->id))
                        {
                            $product[$key]->is_like = 1;
                        }
                    }
                    return response([
                        "status"=> "1",
                        "products"=> json_decode(json_encode($product), true),
                        "shop" => json_decode(json_encode($shop[0]), true),
                        // "category" => json_decode(json_encode($category[0]), true),
                        "image_base_url" => asset('images/')
                    ],200);
                } else {
                    return response([
                        "status"=> "0",
                        "products"=> []
                    ]);
                }
            }
        } else {
            return response([
                'status'=> '0',
                'message'=> 'Invalid shop ID.'
            ], 200);
        }
    }

    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response([
                'status'  => "0",
                'message' => "Product not found"
            ], 404);
        }

        // Delete product images from folder
        $images = json_decode($product->images, true) ?? [];
        foreach ($images as $img) {
            $path = public_path('images/' . $img);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        // Delete the product
        if ($product->delete()) {
            return response([
                "status"  => "1",
                "message" => "Product and its images deleted successfully"
            ], 200);
        } else {
            return response([
                "status"  => "0",
                "message" => "Something went wrong"
            ], 500);
        }
    }


    public function products($id)
    {
        $products = Product::orderBy("id","desc")->paginate(30);
        // print_r(json_decode(json_encode($products), true));
        if(count($products) > 0)
        {
            return response([
                "status"=> "1",
                "products"=> json_decode(json_encode($products), true),
            ],200);
        } else {
            return response([
                "status"=> "0",
                "message"=> "Products not found"
            ],200);
        }
    }

    public function searchProduct(Request $request)
    {
        $attrs = $request->validate([
            'string' => 'required'
        ]);

        $products = DB::select('SELECT * FROM products WHERE p_name LIKE :pname', ['pname' => '%' . $attrs['string'] ."%"]);
        // print_r($products);
        return response([
            'status' => 1,
            'products' => json_decode(json_encode($products), true)
        ]);
    }

    public function sellerProducts()
    {
        $user = auth()->user();
        $count = Product::where('created_by', $user->id)->count();
        $products = DB::table('products')->where('created_by', $user->id)->orderByDesc('id')->paginate(10);

        return response([
            'status' => 1,
            'totalProducts' => $count,
            'products' => json_decode(json_encode($products), true)
        ]);
    }

   
}
