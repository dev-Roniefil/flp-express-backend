<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'username' => 'nullable|string|unique:users,username',
            'company' => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city' => 'nullable|string',
            'postcode' => 'nullable|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'phone' => 'nullable|string',
            'role' => 'in:customer,shop_manager,editor,administrator,super_admin'
        ]);

        $user = User::create([
            'name' => $request->full_name,           // ← Important: map to name
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company' => $request->company,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'postcode' => $request->postcode,
            'country' => $request->country,
            'state' => $request->state,
            'phone' => $request->phone,
            'role' => $request->role ?? 'customer',
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'username' => 'nullable|string|unique:users,username,' . $id,
            'company' => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'city' => 'nullable|string',
            'postcode' => 'nullable|string',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'phone' => 'nullable|string',
            'role' => 'in:customer,shop_manager,editor,administrator,super_admin'
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('username', $request->username)->first();

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}