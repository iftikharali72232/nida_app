<?php
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class ChatApiController extends Controller
{
    public function index()
    {
        // Fetch distinct customers with their latest chat
        $chats = Chat::select('chats.*')
            ->join('users', 'users.id', '=', 'chats.customer_id')
            ->whereIn('chats.id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('chats')
                    ->groupBy('customer_id');
            })
            ->orderBy('chats.created_at', 'desc')
            ->get();

        return view('chats.index', compact('chats'));
    }

    public function show($customerId)
    {
        // Fetch all chats for a specific customer
        $customer = User::findOrFail($customerId);
        $chats = Chat::where('customer_id', $customerId)
                    ->orderBy('created_at', 'asc')
                    ->get();

        // Mark unread messages as read
        Chat::where('customer_id', $customerId)->where('is_admin', 0)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return view('chats.show', compact('customer', 'chats'));
    }

    /**
     * Get all chats for a specific customer.
     */
    public function getChats()
    {
        $customerId = auth()->user()->id;
        $chats = Chat::where('customer_id', $customerId)->orderBy('created_at', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $chats,
        ]);
    }

    /**
     * Store a new chat message.
     */
    public function storeChat(Request $request)
    {
        $validated = $request->validate([
            'text' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = $imageName;
            }
        }
        $audios = "";
        if ($request->hasFile('audio')) {
            // foreach ($request->file('audios') as $audio) {
                $audio = $request->file('audio');
                $audioName = time() . '_' . $audio->getClientOriginalName();
                $audio->move(public_path('phase/audios'), $audioName);
                $audios = $audioName;
            // }
        }
        $chat = Chat::create([
            'customer_id' => auth()->user()->id,
            'text' => $validated['text'],
            'images' => json_encode($imagePaths),
            'audios' => $audios
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Chat message created successfully.',
            'data' => $chat,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'audio' => 'nullable|mimes:mp3,wav',
        ]);
    
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = $imageName;
            }
        }
    
        $audios = null;
        if ($request->hasFile('audio')) {
            $audio = $request->file('audio');
            $audioName = time() . '_' . $audio->getClientOriginalName();
            $audio->move(public_path('phase/audios'), $audioName);
            $audios = $audioName;
        }
    
        $chat = Chat::create([
            'customer_id' => $request->input('customer_id'), // Admin specifies the customer
            'text' => $validated['text'],
            'images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
            'audios' => $audios,
            'is_admin' => 1, // Indicate the message is from the admin
        ]);
        if($chat)
        {
            $user = User::find($request->input('customer_id'));
            $data = [
                'is_user' => $user->user_type == 1 ? 1 : 0,
                'device_token' => $user->device_token,
                'body' => $validated['text'],
                'title' => 'New Message Received From '.$user->name,
                'request_id' => $chat->id,
                'type' => "chat_message"
            ];
            // print_r(sendNotification($data)); exit;

        }
        return back()->with('success', 'Message sent successfully!');
    }


    
}
