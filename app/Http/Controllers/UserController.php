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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json($user, 201);
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


    // public function register(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'name'     => 'required|string|max:500',
    //             'email'    => 'required|string|email|unique:users,email',
    //             'password' => 'required|string|min:6|confirmed',
    //             'role'     =>  'string'
    //         ]);

    //         $user = User::create([
    //             'name'     => $request->name,
    //             'email'    => $request->email,
    //             'password' => Hash::make($request->password),
    //             'role'      =>$request->role
    //         ]);

    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         return response()->json([
    //             'message'      => 'User registered successfully',
    //             'access_token' => $token,
    //             'token_type'   => 'Bearer',
    //             'user'         => $user
    //         ]);
    //     } catch (Exception $tr) {
    //         dd($tr);
    //     }
    // }

    public function register(Request $request)
    {
        try {

            // dd($request ->all());
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'role'     => 'nullable|in:user,admin,editor', // validate against allowed roles
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'] ?? 'user', // default role if not passed
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'      => 'User registered successfully',
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => $user
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 🔐 User Login
    // public function login(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'email'    => 'required|email',
    //             'password' => 'required',
    //              'role'     => 'required',
    //         ]);

    //         $user = User::where('email', $request->email)->first();

    //         if (!$user || !Hash::check($request->password, $user->password)) {
    //             throw ValidationException::withMessages([
    //                 'email' => ['The provided credentials are incorrect.']
    //             ]);
    //         }

    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         return response()->json([
    //             'message'      => 'Login successful',
    //             'access_token' => $token,
    //             'token_type'   => 'Bearer',
    //             'user'         => $user
    //         ]);
    //     } catch (Exception $e) {
    //         dd($e);
    //     }
    // }
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
