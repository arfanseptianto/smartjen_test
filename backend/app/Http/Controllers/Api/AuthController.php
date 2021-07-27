<?php

namespace App\Http\Controllers\Api;

// use Session;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
                $school_id = $school->where('name',$request->school_name)->first()->id;
            }

            $user->name = $request->user_name;
            $user->email = $request->user_email;
            $user->password = Hash::make($request->user_password);
            $user->role = $request->user_role;
            $user->school_id = $school_id;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User register successfully',
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
                'code' => $ex->getCode(),
            ]);
        }
    }

    /**
     * Login
     */
    public function login(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validate->fails()) {
                $respon = [
                    'success' => 'false',
                    'message' => 'Please check again your school name, email address, or password'
                ];
                return response()->json($respon, 401);
            } else {
                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password
                ];
                // $credentials = Arr::add($credentials, 'status', 'aktif');
                if (!Auth::attempt($credentials)) {
                    $respon = [
                        'success' => 'false',
                        'message' => 'Please check again your school name, email address, or password'
                    ];
                    return response()->json($respon, 401);
                }

                if(Auth::user()->school->name !== $request->school_name) {
                    Auth::logout();
                    $respon = [
                        'success' => 'false',
                        'message' => 'Please check again your school name, email address, or password'
                    ];
                    return response()->json($respon, 401);
                } else {
                    $user = User::where('email', $request->email)->with('school')->first();
                    if (!Hash::check($request->password, $user->password, [])) {
                        throw new \Exception('Error in Login');
                    }

                    $token = $user->createToken('token-auth')->plainTextToken;
                    $respon = [
                        'success'   => true,
                        'data'      => [
                            'id'        => $user->id,
                            'name'      => $user->name,
                            'email'     => $user->email,
                            'role'      => $user->role,
                            "school"    => [
                                'id'        => $user->school->id,
                                'name'      => $user->school->name,
                                'address'   => $user->school->address,
                                'phone'     => $user->school->phone,
                            ],
                            'token'     => $token
                        ],
                    ];
                    return response()->json($respon, 200);
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
                'code' => $ex->getCode(),
            ]);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request) {
        try {
            $user = $request->user();
            // $user->currentAccessToken()->delete(); // If prefer to delete current session
            $user->tokens()->delete();
            $respon = [
                'success' => true,
                'message' => 'Logout successfully',
            ];
            return response()->json($respon, 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
                'code' => $ex->getCode(),
            ]);
        }
    }
}
