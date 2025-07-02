<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('team')->where('user_type', 2)->get();
        return view('team_users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teams = Team::all();
        return view('team_users.create', compact('teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'team_id' => 'required|exists:teams,id',
        ]);

        User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'team_id' => $request->team_id,
            'user_type' => 2,
            'status' => 1
        ]);

        return redirect()->route('team_users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $teams = Team::all();
        return view('team_users.edit', compact('user', 'teams'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'team_id' => 'required|exists:teams,id',
            'status' => 'nullable|int'
        ]);

        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($request->password);
        }
        $user->update($validatedData);

        return redirect()->route('team_users.index')->with('success', 'User updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('team_users.index')->with('success', 'User deleted successfully.');
    }
}
