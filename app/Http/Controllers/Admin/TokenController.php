<?php

namespace App\Http\Controllers\Admin;

// app/Http/Controllers/Api/TokenController.php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Token;

class TokenController extends Controller
{
    // ✅ Get all assigned tokens
    public function index()
    {
        $tokens = Token::where('status', 'assigned')
            ->orderBy('token_number', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $tokens
        ]);
    }

    // ✅ Assign a token based on mobile
    public function assign(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|max:20',
            'token_number' => 'nullable|integer'
        ]);


        $token = Token::create([
            'token_number' => $request->token_number,
            'mobile' => $request->mobile,
            'status' => 'assigned'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Token assigned successfully',
            'data' => $token
        ]);
    }
}

