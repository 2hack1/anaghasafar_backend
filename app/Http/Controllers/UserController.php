<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
        try {
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
        } catch (Exception $e) {
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





    public function generateAndSendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not found'
            ], 404);
        }

        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP in cache for 10 minutes, key unique per user
        Cache::put('otp_' . $user->id, $otp, now()->addMinutes(10));

        // Call the email sending function from another controller
        $sendEmailController = new EmailController();
        $sendEmailController->forgetUserPassSendEmail($request->email, $otp);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP has been sent to your email'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not found'
            ], 404);
        }

        $cachedOtp = Cache::get('otp_' . $user->id);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            // OTP is valid, remove it from cache
            Cache::forget('otp_' . $user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid or expired OTP'
        ]);
    }






    public function updatePassword(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            // 'otp' => 'required|numeric',
            'new_password' => 'required|string|min:6|confirmed', // expects 'new_password_confirmation'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->new_password);
        $user->save();
        $sendEmailController = new EmailController();
        $sendEmailController->updatedPass($request->email,$request->new_password);

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully'
        ]);
    }
}
