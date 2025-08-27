<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // ✅ Create a new user
    public function register(Request $request)
{
    return $this->store($request); // reuse store()
}
    public function store(Request $request)
    {
        try{
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
             'user_mob_no1' => 'nullable|string|max:15',
           'user_mob_no2' => 'nullable|string|max:15',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
              'user_mob_no1' => $validated['user_mob_no1'] ?? null,
         'user_mob_no2' => $validated['user_mob_no2'] ?? null,
        ]);

        return response()->json($user, 201);
    }catch(Exception $e){
        dd($e);
    }
}


    public function show($id)
   {
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    return response()->json($user, 200);
}

    // ✅ Update user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($request->only(['name', 'email']));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json($user, 200);
    }

    // ✅ Delete user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }


    
    public function login(Request $request)
{
    try {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required|in:user,admin,editor',
        ]);

        $user = User::where('email', $request->email)->first();

        if (
            !$user ||
            !Hash::check($request->password, $user->password) ||
            $user->role !== $request->role
        ) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect or role does not match.']
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Login successful',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    // 🔓 User Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    // 👤 Get Authenticated User Info
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
