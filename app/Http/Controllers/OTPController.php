<?php

namespace App\Http\Controllers;

    use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OTPController extends Controller
{

public function showForm()
{
    return view('auth.verify_otp');
}

public function verify(Request $request)
{
    $request->validate(['otp' => 'required']);
    $user = Auth::user();

    if ($user->otp == $request->otp) {
        $user->is_verified = true;
        $user->otp = null;
        $user->save();
        return redirect('/home')->with('success', 'Email verified!');
    }

    return back()->with('error', 'Invalid OTP');
}

}
