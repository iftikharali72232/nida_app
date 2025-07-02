<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\NotificationController;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Models\CardDetail;
use App\Models\Notification;
use App\Models\Shop;
use App\Rules\ValidMobileNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    //Register User
    public function register(Request $request)
    {
        // print_r($request->name_ar); exit;
        $attrs = $request->validate([
            "name"=> "required|string",
            "password"=> "required|min:6",
            'mobile' => 'required|unique:users',
            'user_type'=> 'required|int',
        ]);
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($request);
        }
        $ussr = User::where('email', $request->email)
            ->whereNotNull('email')
            ->first();

        if($ussr)
        {
            return response([
                "message" => "Email is duplicate, please try with another email..!",
                'user' => $ussr
            ]);
        }
        $status = 1;
        if($request->user_type == 2)
        {
            $status = 0;
        }
        $randomNumber = rand(100000, 999999);
        $user = User::create([
            "name"=> $attrs["name"],
            "name_ar"=> $request->name_ar,
            "email"=> $request->email ?? NULL,
            "mobile" => $attrs["mobile"],
            "user_type" => $attrs['user_type'],
            "password"=> bcrypt($attrs["password"]),
            "image"=> $file_name,
            "otp"=> $randomNumber,
            "street_address" => $request->address,
            "status"=> $status,
            "country" => $request->country,
        ]);
        if(!empty($file_name))
        {
            $imageUrl = asset('images/'.$file_name);
            $user['imageUrl'] = $imageUrl;
        } 
       
        if($user)
        {
             Wallet::create([
                'user_id' => $user->id
            ]);
            $data = [
                'user_id' => $user->id,
                'text_en' => "Account created successfully.",
                'text_ar' => "تم إنشاء الحساب بنجاح.",
                'request_id' => 0,
                'page' => $request->page
            ];
            storeNotification($data);
            return response([
                'users' => $user,
                'token' => $status == 1 ? $user->createToken('secret')->plainTextToken : "",
            ]);
        } else {
            return response([
                "message" => "Something went wrong."
            ]);
        }
        
    }

    public function bankList()
    {
        $banks = Bank::where('status', 1)->get();
        return response()->json(['banks' => $banks]);
    }
    public function updateVehicle(Request $req)
    {
        $attrs = $req->validate([
            'number_plate' => 'required',
            'vehicle_type' => 'required|int',
            'driving_license' => 'required',
        ]);

        $update = DB::table('users')->where('id', auth()->user()->id)->update([
            'number_plate' => $req->number_plate,
            'category_id' => $req->vehicle_type,
            'driving_license' => $req->driving_license,
        ]);

        return response()->json(['success' => 'success']);
    }
    public function createDriver(Request $req)
    {
        $attrs = $req->validate([
            "name"=> "required|string",
            "email"=> "required|email|unique:users,email",
            "password"=> "required|min:6|confirmed",
            'mobile' => 'required|unique:users',
            'user_type'=> 'required|int',
            'vehicle_type' => 'required|int',
            'driving_license' => 'required',
            'bank_id' => 'required|int',
            'bank_account' => 'required',

        ]);
        $randomNumber = rand(100000, 999999);
        $user = User::create([
            "name"=> $attrs["name"],
            "email"=> $attrs["email"],
            "mobile" => $attrs["mobile"],
            "user_type" => $attrs['user_type'],
            "password"=> bcrypt($attrs["password"]),
            "otp"=> $randomNumber,
            "street_address" => $req->address,
            "status"=> 1,
            "category_id" => $req->vehicle_type,
            "driving_license" => $req->driving_license,
            "bank_id" => $req->bank_id,
            "bank_account" => $req->bank_account,
            "name_ar" => $req->name_ar,
        ]);
        if($user)
        {
            Wallet::create([
                'user_id' => $user->id
            ]);
            $notification = new Notification();
            $notification->user_id = $user->id; // Assuming the user is authenticated
            $notification->message = 'Your account registered Successfully';
            $notification->page = 'profile';
            $notification->save();
            return response([
                'users' => $user,
                'token' => $user->createToken('secret')->plainTextToken,
            ]);
        } else {
            return response([
                "message" => "Something went wrong."
            ]);
        }
    }
    //Register User
    public function sellerRegister(Request $request)
    {
        // print_r($request->name_ar); exit;
        $attrs = $request->validate([
            "name"=> "required|string",
            "email"=> "required|email|unique:users,email",
            "password"=> "required|min:6|confirmed",
            'mobile' => 'required|unique:users',
            'user_type'=> 'required|int',
            'shop_name' => 'required',
            'category_id' => 'required',
            'reg_no' => 'required'
        ]);
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($request);
        }
        $randomNumber = rand(100000, 999999);
        $user = User::create([
            "name"=> $attrs["name"],
            "name_ar"=> $request->name_ar,
            "email"=> $attrs["email"],
            "mobile" => $attrs["mobile"],
            "user_type" => $attrs['user_type'],
            "password"=> bcrypt($attrs["password"]),
            "image"=> $file_name,
            "otp"=> $randomNumber,
            "street_address" => $request->address,
            "status"=> 0,
            "country" => $request->country,
        ]);
        if(!empty($file_name))
        {
            $imageUrl = asset('images/'.$file_name);
            $user['imageUrl'] = $imageUrl;
        } 
       
        if($user)
        {
            // Create New record
            $shop = Shop::create([
                "name"=> $_POST["shop_name"],
                "category_id"=> $_POST['category_id'],
                "reg_no"=> $_POST['reg_no'],
                "created_by"=> $user->id,
            ]);

            Wallet::create([
                'user_id' => $user->id
            ]);
            return response([
                'users' => $user,
                'token' => $user->createToken('secret')->plainTextToken,
            ]);
        } else {
            return response([
                "message" => "Something went wrong."
            ]);
        }
        
    }

    public function setLocation(Request $req)
    {
        $req->validate([
            // 'city' => 'required|string',
            // 'street_address' => "required",
            // "state" => "required",
            // "postal_code" => "required",
            "latitude" => "required",
            "longitude" => "required"
        ]);

        
        $user = auth()->user();
        $data = [
            'city' => $req->city ?? $user->city,
            'street_address' => $req->street_address ?? $user->street_address,
            "state" => $req->state ?? $user->state,
            "postal_code" => $req->postal_code ?? $user->postal_code,
            "latitude" => $req->latitude ?? $user->latitude,
            "longitude" => $req->longitude ?? $user->longitude,
        ];      

        $update = User::where('id', $user->id)->update($data);
        // print_r($user); exit;
       
        if($update){
            return response([
                "status" => 1,
                "msg" => "success"
            ]);
        } else {
            return response([
                "status" => 0,
                "msg" => "Something went wrong"
            ]);
        }
    }
    public function updateUser(Request $request)
    {
        // print_r($request->name_ar); exit;
        $attrs = $request->validate([
            "name"=> "required|string",
            // "email"=> "required|email",
            'mobile' => 'required',
        ]);
        
        $user = auth()->user();
        // print_r($user); exit;
        if($user->user_type == 0)
        {
            return response([
                "status" => 0,
                "message" => "This is not a valid user."
            ]);
        }
        $data = DB::select("SELECT * FROM users WHERE email=:email AND id != :id",[':email' => $attrs['email'], ':id' => $user->id]);
        // print_r($data); exit;
        if(count($data) > 0)
        {
            return response([
                "status" => 0,
                "message" => "Email already taken."
            ]);
        }
        $data = DB::select("SELECT * FROM users WHERE mobile=:mobile AND id != :id",[':mobile' => $attrs['mobile'], ':id' => $user->id]);
        // print_r($data); exit;
        if(count($data) > 0)
        {
            return response([
                "status" => 0,
                "message" => "Mobile number already taken."
            ]);
        }
        $file_name = "";
        if(isset($_FILES['image']))
        {
            removeImages($user->image); 
            $file_name = $this->upload($request);
        }

          
        $user = DB::table("users")->where("id","=", $user->id)->update([
            "name"=> $attrs["name"],
            "email"=> $attrs["email"],
            "mobile" => $attrs["mobile"],
            "image" => $file_name != "" ? $file_name : $user->image,
            'city' => $request->city ?? $user->city,
            'street_address' => $request->street_address ?? $user->street_address,
            "state" => $request->state ?? $user->state,
            "postal_code" => $request->postal_code ?? $user->postal_code,
            "latitude" => $request->latitude ?? $user->latitude,
            "longitude" => $request->longitude ?? $user->longitude,
        ]);
        
       

            return response([
                'status' => 1,
                'message' => "User Updated Successfully",
            ]);
        
    }
  // login user
  public function login(Request $request)
  {
      $attrs = $request->validate([
          "mobile"=> "required|string",
          "password"=> "required|min:6",
          "device_token" => "required",
          'user_type' => 'required|int'
      ]);
      $data = $attrs;
      unset($data['device_token']);
     $user = User::where('mobile', $attrs['mobile'])->first();
     if(!$user)
     {
        $user = User::where('email', $attrs['mobile'])->first();
     }
     if($user)
     {//    print_r($user->mobile); exit;
          if($user->user_type == 1 && $user->status == 0)
          {
              return response([
                  'message' => "Your account is inactive pls contact to the support to make active your account.",
              ], 403);
          } else if($user->user_type == 2 && $user->status == 0)
          {
              return response([
                  'message' => "Your account status is inactive pls contact to the support to make active your account.",
              ], 403);
          }
          if($user->user_type != $request->user_type)
          {
                return response([
                    'message' => "user_type does not matched",
                ]);
          }
          if(!Auth::attempt($data)) {
              return response([
                  'message' => "Invalid Credentials.",
              ], 403);
          }

          DB::table('users')->where('id', $user->id)->update([
              'device_token' => $attrs['device_token']
          ]);
          $user = User::where('mobile', $attrs['mobile'])->first();
          if(!$user)
          {
             $user = User::where('email', $attrs['mobile'])->first();
          }
              return response([
                  'user' => $user,
                  'token' => auth()->user()->createToken('secret')->plainTextToken,
              ], 200);
     } else {
          return response([
              'message' => "User not found",
          ], 403);
     }
  
  }

    //logout user
    public function logout(){
        auth()->user()->tokens()->delete();
        return response([
            'message'=> 'Logout success.',
            ],200);
    }

    // user detail
    public function user(){
        
        $user = auth()->user();
        if($user)
        {
            $file_name = auth()->user()->image;
            if(!empty($file_name))
            {
                $imageUrl = asset('images/'.$file_name);
                auth()->user()->imageUrl = $imageUrl;
            } 
            return response([
                'user'=> json_decode(json_encode($user), true),    
            ], 200);

        } else {
            return response([
                'message'=> 'SESSION expired',    
            ], 200);
        }
    }

    public function reset(Request $request){
        $attrs = $request->validate([
            'mobile'=> 'required',
            "password"=> "required|min:6|confirmed",
        ]);

        $users = DB::select('SELECT * FROM users WHERE mobile=:mobile AND id > 0', [':mobile' => $attrs['mobile']]);
        // print_r($users);
        if(count($users) > 0){
            $user = DB::update('UPDATE users SET password=:password WHERE id=:id', [':password' => bcrypt($attrs["password"]), ':id' => $users[0]->id]);
            if($user)
            {
                $notification = new Notification();
                $notification->user_id = $users[0]->id; // Assuming the user is authenticated
                $notification->message = 'Your account registered Successfully';
                $notification->page = 'profile';
                $notification->save();
                return response([
                    'message' => "Password Update Successfully.",
                ], 200);
            }
        }else {
            return response([
                'message' => "User not found.",
            ], 200);
        }
    }

    public function otpVarification(Request $request){
        $attrs = $request->validate([
            "otp"=> "required|max:6|string",
            // "user_id"=> 'required|int',
        ]);

        $user = DB::select('SELECT * FROM users WHERE otp=:otp', [':otp'=> $attrs['otp']]);
        $user = json_decode(json_encode($user), true)[0];
        print_r(decrypt($user['password'])); exit;
        if(count($user) > 0){
            $attrs = [
                'mobile' => $user['mobile'],
                'password'=> decrypt($user['password']),
            ];
            $userUpdate = DB::update('UPDATE users SET status=:status WHERE id=:id', [':id' => $user['id'],':status' => 1]);
            if($userUpdate)
            {
                if(!Auth::attempt($attrs)) {
                    return response([
                        'message' => "Invalid OTP code.",
                    ], 403);
                   }
            
                    // return redirect()->route("")->with("success","");
                    return response([
                        'user' => auth()->user(),
                        'token' => auth()->user()->createToken('secret')->plainTextToken,
                    ], 200);
            }else{
                if(!Auth::attempt($attrs)) {
                    return response([
                        'message' => "Invalid OTP code.",
                    ], 403);
                   }
            
                    // return redirect()->route("")->with("success","");
                    return response([
                        'user' => auth()->user(),
                        'token' => auth()->user()->createToken('secret')->plainTextToken,
                    ], 200);
            }
        }else{
            return response([
                'message'=> 'OTP invalid',
                ], 200);
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
    public function delete()
    {
        $user = User::find(auth()->user()->id);
        // $categories = DB::select('SELECT id FROM categories WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        // $shops = DB::select('SELECT id FROM shops WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        // $products = DB::select('SELECT id FROM products WHERE created_by=:uid', [':uid'=> auth()->user()->id]);
        // print_r($categories); exit;
        if($user){
            if($user->delete())
            {
                return response([
                    'status'=> 'success',
                    'message' => "User Delete successfully"
                ], 200);
            }else if($user) {
                return response([
                    "status"=> "success",
                    "message"=> "You cannot delete this user, This is use in category, shops and products."
                ],200);
            }
        } else {
            return response([
                "status"=> "success",
                "message"=> "User not found"
            ],200);
        }
    }

    public function resetRequest(Request $request)
    {
        $attrs = $request->validate([
            "mobile"=> "required",
        ]);

        $user = DB::select("SELECT * FROM users WHERE mobile=:mobile", [":mobile"=> $attrs['mobile']]);
        if($user)
        {
            return response([
                'status'=> 'success',
                'otp' => $user[0]->otp
            ],200);
        }else {
            return response([
                'status'=> 'success',
                'message'=> 'user Not found'
            ],200);
        }
    }
     
    public function userList($type = 1)
    {
        $users = DB::table('users')->where('user_type', $type)->get();

        if(count($users) > 0)
        {
            return response([
                'status'=> '1',
                'users' => json_decode(json_encode($users), true),
            ],200);
        } else {
            return response([
                'status'=> '0',
                'message' => "Users not found"
            ],200);
        }
    }
    public function updateProfileImage($id, Request $req)
    {
        $file_name = "";
        if(isset($_FILES['image']))
        {
            $file_name = $this->upload($req);
        }

        $update = DB::table("users")->where("id", $id)->update([
            'image' => $file_name
        ]);
        if($update){
            return response([
                "status" => 1,
                "image_url" => asset("/images/".$file_name)
            ]);
        } else {
            return response([
                "status" => 0,
                "message" => "Something went wrong"
            ]);
        }
    }

    public function cardDetail(Request $req)
    {
        $data = $req->validate([
            'card_number' => "required",
            "cvv" => "required|int",
            "month" => "required|int",
            "year" => "required|int",
        ]);

        $card_data = CardDetail::create([
            'card_number' => $data['card_number'],
            "cvv" => $data['cvv'],
            "month" => $data['month'],
            "year" => $data['year'],
            "user_id" => auth()->user()->id
        ]);

        if($card_data)
        {
            return response([
                'status' => 1,
                "card" => json_decode(json_encode($card_data), true)
            ]);
        } else {
            return response([
                'status' => 0,
                "message" => "Something went wrong."
            ]);
        }
    }

    public function cardDetailUpdate($id,Request $req)
    {
        $data = $req->validate([
            'card_number' => "required",
            "cvv" => "required|int",
            "month" => "required|int",
            "year" => "required|int",
        ]);

        $card_data = CardDetail::where('id',$id)->update([
            'card_number' => $data['card_number'],
            "cvv" => $data['cvv'],
            "month" => $data['month'],
            "year" => $data['year'],
        ]);

        if($card_data)
        {
            return response([
                'status' => 1,
                "message" => "Update Successfully."
            ]);
        } else {
            return response([
                'status' => 0,
                "message" => "Something went wrong."
            ]);
        }
    }

    public function deleteCardDetails($id)
    {
        $card = CardDetail::find( $id );
        
            if($card->delete())
            {
                return response([
                    'status'=> '1',
                    'message' => "Card detail delete successfully."
                ], 200);
            }else if($card) {
                return response([
                    "status"=> "0",
                    "message"=> "Some thing went wrong."
                ],200);
            }
    }

    public function updateUserData(Request $request, $id)
    {
        // Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'mobile' => 'required|string|max:15|unique:users,mobile,' . $id,
            'password' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        // Find the user
        $user = User::findOrFail($id);

        // Update fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->mobile = $validated['mobile'];

        // Hash password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle image upload if provided
        if(isset($_FILES['image']))
        {
            removeImages($user->image); 
            $user->image = $this->upload($request);
        }

        // Save updated user
        $user->save();

        // Return response
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }
    function send_push_notification(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'request_id' => 'required',
            'device_token' => 'required'
        ]);

        $user = auth()->user();
        if($user->user_type == 1)
        {
            $data['is_user'] = 1;
        }
        // $data['device_token'] = $user->device_token;

        return response()->json(['msg' => sendNotification($data)]);
    }
}
