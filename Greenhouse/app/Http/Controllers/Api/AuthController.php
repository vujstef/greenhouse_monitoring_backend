<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use HttpResponses;

    private $success;

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->only(['email', 'password']));

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('API Token')->plainTextToken;
        /*
  return $this->success([
      'user' => $user,
      'token' => $token
  ]);
      */
        return response()->json(['token' => $token, 'roleId' => $user->role, 'id' => $user->id]);
    }

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->only(['first_name', 'last_name', 'email', 'password', 'role']));

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role === 'admin' ? 1 : 0,
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        $this->success = $this->success([
            'message' => 'You have successfully been logged out and your token has been removed'
        ]);
    }

    public function adminLogout()
    {
        Auth::user()->currentAccessToken()->delete();

        $this->success = $this->success([
            'message' => 'You have successfully been logged out as an admin and your token has been removed.'
        ]);
    }

    public function submitForgetPasswordForm(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'exists:users']]);
        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $resetUrl = url('http://localhost:3000/resetPassword/' . $token);
        $message = "Dear User,\n\nWe have received a request to reset your password. Please click the following link to reset your password:\n\n";
        $message .= $resetUrl;
        $message .= "\n\nIf you did not request a password reset, please ignore this email.\n\nThank you,\n\nYour Company";

        Mail::raw($message, function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json(['message' => 'We have e-mailed your password reset link!']);
    }

    public function resetPassword(Request $request, $token)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $token
            ])
            ->first();

        if (!$updatePassword) {
            return response()->json(['error' => 'Invalid token!'], 400);
        }

        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return response()->json(['message' => 'Your password has been changed!']);
    }

    public function updateUserData(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $userData = $request->validated();

        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return response()->json(['message' => 'User updated successfully']);
    }

    function getLoggedInUserId()
    {
        $user = Auth::user();
        if ($user) {
            return $user->id;
        }
        return null;
    }
}
