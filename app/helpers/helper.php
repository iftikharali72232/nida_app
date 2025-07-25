<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Notification;
use App\Models\Shop;
use App\Models\Token;
use App\Models\User;

function removeImages($imageArray, $multi_images = 0) {
    // print_r($imageArray); exit;
    if($multi_images == 1)
    {
        foreach($imageArray as $img)
        {
            if(File::exists(public_path('/images/'.$img))) {
            //     echo "success"; exit;
                File::delete(public_path('/images/'.$img));
            }
        }
    } else {
        if(File::exists(public_path('/images/'.$imageArray))) {
            //     echo "success"; exit;
                File::delete(public_path('/images/'.$imageArray));
            }
    }
    
}

function formatDateTimeToEnglish($dateTimeString)
{
    $currentFormat = "Y-m-d H:i:s";
    // Parse the input date and time string using Carbon
    $dateTime = Carbon::createFromFormat($currentFormat, $dateTimeString);

    // Format the date and time to English with AM/PM
    $formattedDateTime = $dateTime->format('l, F j, Y g:i A');

    return $formattedDateTime;
}

function formatCreatedAt($created_at) {
    // Convert the created_at string to a DateTime object
    $createdDateTime = new DateTime($created_at);
    
    // Get the current date and time
    $currentDateTime = new DateTime();

    // Calculate the difference between the current date and the created_at date
    $interval = $currentDateTime->diff($createdDateTime);

    // Check the difference and format accordingly
    if ($interval->d > 0) {
        // Less than one hour, show in minutes
        return $interval->d . trans('lang.days_ago');
    } elseif ($interval->h < 24) {
        // Less than 24 hours, show in hours
        return $interval->h . trans('lang.hours_ago');
    } else {
        // More than 24 hours, show in days
        return $interval->i . trans('lang.minutes_ago');
    }
}
function generateRandomCode() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    $max = strlen($characters) - 1;
    
    for ($i = 0; $i < 10; $i++) {
        $code .= $characters[mt_rand(0, $max)];
    }
    
    return $code;
}
function send_message($data, $mobile)
{
    $ch = curl_init();

    $payload = json_encode([
        "messaging_product" => "whatsapp",
        "recipient_type" => "individual",
        "to" => $mobile,
        "type" => "template",
        "template" => [
            "name" => "parcel_template_code",
            "language" => ["code" => "en"],
            "components" => [
                [
                    "type" => "body",
                    "parameters" => [
                        [
                            "type" => "text",
                            "text" => $data['code']
                        ]
                    ]
                ]
            ]
        ]
    ]);
    curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v16.0/116750164666647/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $headers = array();
    $headers[] = 'Authorization: Bearer EAAHpsDJRFp0BO4BuWb3p8La3Nte3E8wccZCy5m4QJMV7X1NbSugSKVjZAy4nEBI2wevVpbDQ9RFKQdlHNeSwbDCA4GEzSxw4Rg8913V7u8LGin7vlbQymwHpWhCllY20xRSncKB0F026oq5jgKWM6fzxooX0H8jMc4YrputVvwQwvgDoDIF4ZBsbtR0iwCvAU7zZC6zz3ZAKI6rqG';
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    $result = json_decode($result, true);
    return $result;
}
function cleanText($text) {
    // Remove newlines and tabs
    $text = str_replace(array("\n", "\r", "\t"), ' ', $text);

    // Replace multiple spaces with a single space
    $text = preg_replace('/ {2,}/', ' ', $text);

    // Replace more than four consecutive spaces with four spaces
    $text = preg_replace('/ {5,}/', '    ', $text);

    return $text;
}

function subtractFivePercent($amount) {
    // Calculate 5% of the amount
    $percentage = $amount * 0.05;

    // Subtract 5% from the original amount
    $remainingAmount = $amount - $percentage;

    return $remainingAmount;
}

function getFivePercent($amount) {
    // Calculate 5% of the amount
    $percentage = $amount * 0.05;


    return $percentage;
}

    function storeNotification(array $data)
    {
        return Notification::create([
            'user_id'    => $data['user_id'] ?? null,
            'text_en'    => $data['text_en'] ?? '',
            'text_ar'    => $data['text_ar'] ?? '',
            'request_id' => $data['request_id'] ?? null,
            'page'       => $data['page'] ?? null,
        ]);
    }
    function getOrderStatusText($status, $lang = 'en')
    {
        $statuses = [
            'en' => [
                0 => 'Pending',
                1 => 'Processing',
                2 => 'Complete',
                3 => 'Canceled',
                4 => 'Deleted',
            ],
            'ar' => [
                0 => 'قيد الانتظار',
                1 => 'قيد المعالجة',
                2 => 'مكتمل',
                3 => 'ملغي',
                4 => 'محذوف',
            ]
        ];
    
        return $statuses[$lang][$status] ?? ($lang == 'ar' ? 'غير معروف' : 'Unknown');
    }
    
    function sendNotification($data)
    {
        // $deviceToken = $data['device_token'];
        // $title = $data['title'];
        // $body = $data['body'];
        // // $subtitle = $data['subtitle'];
        // $serverKey = $data['is_driver'] == 1 ? env('DRIVER_SERVER_KEY') : env('USER_SERVER_KEY');  // Assuming server key is sent in request for simplicity
        if(isset($data['is_user']) && $data['is_user'] == 1)
        {
            $url = 'https://fcm.googleapis.com/v1/projects/'.getenv('USER_PROJECT_ID').'/messages:send';

            // Set your client credentials and refresh token
            $client_id = getenv('GOOGLE_CLIENT_ID');
            $client_secret = getenv('GOOGLE_CLIENT_SECRET');
            $refresh_token = getenv('GOOGLE_REFRESH_TOKEN'); // Replace with your actual refresh token
        } else {
            $url = 'https://fcm.googleapis.com/v1/projects/'.getenv('USER_PROJECT_ID_W').'/messages:send';
    
            // Set your client credentials and refresh token
            $client_id = getenv('GOOGLE_CLIENT_ID_W');
            $client_secret = getenv('GOOGLE_CLIENT_SECRET_W');
            $refresh_token = getenv('DELIVERY_REFRESH_TOKEN_W'); // Replace with your actual refresh token

        }
        // echo $url; exit;
        $token_url = 'https://oauth2.googleapis.com/token';

        // Prepare the POST fields
        $post_fields = [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        // Execute the cURL request and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            die('Curl error: ' . curl_error($ch));
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the JSON response
        $response_data = json_decode($response, true);
        // print_r($response_data); exit;
        // Check if the response contains an error
        if (isset($response_data['error'])) {
            die('Error refreshing the token: ' . $response_data['error']);
        }

        // print_r($response_data); exit;
        // Extract the new access token
        $newAccessToken = $response_data['access_token'];
        $headers = [
            'Authorization: Bearer '.$newAccessToken,
            'Content-Type: application/json'
        ];

        $fields = '{
            "message": {
                 "token":"'.$data['device_token'].'",
                 "notification":{
                     "title":"'.$data['title'].'",
                     "body":"'.$data['body'].'"
                 },
                 "data": {
                    "request_id": "'.$data['request_id'].'",
                    "type": "'.(isset($data['type']) ? $data['type'] : "simple_notification").'"
                }
             }
         }';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
        // print_r($ch); exit;
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result, true);
    }

    function fvrt_shop($shop_id)
    {
        $user = Auth::user();
        if($user) {
            $shop = Shop::where('user_id', $user->id)->where('shop_id', $shop_id)->where('fvrt', 1)->first();
            if($shop) {
                return $shop; // Already favorited
            }
            $shop = Shop::create([
                'user_id' => $user->id,
                'fvrt' => 1,
                'visted' => 0,
                'shop_id' => $shop_id,
            ]);
            return $shop;
        }
    }

    function un_fvrt_shop($shop_id)
    {
        $user = Auth::user();
        if($user) {
            $shop = Shop::where('user_id', $user->id)->where('shop_id', $shop_id)->first();
            if($shop) {
                $shop->delete();
                return true;
            }
        }
        return false;
    }

    function last_visited_shop($shop_id)
    {
        $user = Auth::user();
        if($user) {
            $shop = Shop::create([
                'user_id' => $user->id,
                'fvrt' => 0,
                'visted' => 1,
                'shop_id' => $shop_id,
            ]);
            return $shop;
        }
    }
    
 

    function fvrt_shop_list($perPage = 0)
    {
        $user = Auth::user();

        if (!$user) {
            return collect(); // empty
        }

        // Query with join & pagination
        $shops = Shop::where('shops.user_id', $user->id)
            ->where('shops.fvrt', 1)
            ->join('users', 'shops.user_id', '=', 'users.id')
            ->select(
                'shops.*',
                'users.name as user_name',
                'users.mobile as user_mobile',
                'users.email as user_email',
                'users.image as user_image',
                'users.id as user_id'
            );
             if($perPage == 0)
            {
                $shops = $shops->limit(5)->get();
            } else {
                $shops = $shops->paginate($perPage);
            }

        return $shops;
    }

    function last_visited_shop_list($perPage = 0)
    {
        $user = Auth::user();

        if (!$user) {
            return collect(); // empty
        }

        // Query with join & pagination
        $shops = Shop::where('shops.user_id', $user->id)
            ->where('shops.visted', 1)
            ->join('users', 'shops.user_id', '=', 'users.id')
            ->select(
                'shops.*',
                'users.name as user_name',
                'users.mobile as user_mobile',
                'users.email as user_email',
                'users.image as user_image',
                'users.id as user_id'
            );
            if($perPage == 0)
            {
                $shops = $shops->limit(5)->get();
            } else {
                $shops = $shops->paginate($perPage);
            }

            return $shops;    
    }
    function current_user_token()
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

       $token = Token::where('tokens.mobile', $user->mobile)
            ->where('tokens.status', 'assigned')
            ->join('users', 'tokens.user_shop_id', '=', 'users.id')
            ->select(
                'tokens.*',
                'users.name as user_name',
                'users.mobile as user_mobile',
                'users.email as user_email',
                'users.image as user_image',
                'users.id as user_id'
            )
            ->orderByDesc('tokens.id')  // OR ->latest('tokens.created_at') if you have timestamps
            ->first();

        return $token; // null-safe
    }
    function shop_details($shop_id)
    {
        last_visited_shop($shop_id);
        $shop = User::where('id', $shop_id)
            ->select(

                'users.name as user_name',
                'users.mobile as user_mobile',
                'users.email as user_email',
                'users.image as user_image',
                'users.id as user_id'
            )
            ->first();

        return $shop;
    }
