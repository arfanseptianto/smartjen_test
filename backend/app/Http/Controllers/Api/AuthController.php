<?php

namespace App\Http\Controllers\Api;

// use Session;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Signup
     */
    public function signup(Request $request)
    {
        try {
            $user = new User();
            $school = new School();

            if(!$school->where('name',$request->school_name)->exists()){
                $school->name = $request->school_name;
                $school->address = $request->school_address;
                $school->phone = $request->school_phone;
                $school->save();
                $school_id = $school->id;
            } else {
                $school_id = $request->school_id;
            }

            $user->name = $request->user_name;
            $user->email = $request->user_email;
            $user->password = Hash::make($request->user_password);
            $user->role = $request->user_role;
            $user->school_id = $school_id;
            $user->save();

            $success = true;
            $message = 'User register successfully';
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->user_email,
            'password' => $request->user_password,
        ];

        if (Auth::attempt($credentials)) {
            $success = true;
            $message = 'User login successfully';
        } else {
            $success = false;
            $message = 'Unauthorised';
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        try {
            // Session::flush();
            Auth::logout();
            $success = true;
            $message = 'Successfully logged out';
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);
    }
}
