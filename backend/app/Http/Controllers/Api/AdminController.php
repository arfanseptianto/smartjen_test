<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\Mailer;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function get_all_users(Request $request)
    {
        try {
            $user = User::where('school_id',$request->user()->school_id)->where('role', '!=', 1)->orderBy('role')->orderBy('id')->get(['id', 'name', 'email', 'role', 'status']);

            return response()->json([
                'success' => true,
                'message' => 'List All User',
                'total'   => count($user),
                'data'    => $user,
            ], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        }
    }

    public function add_new_user(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|numeric|min:1|max:3',
            ]);

            if ($validate->fails()) {
                $respon = [
                    'success' => 'false',
                    'message' => $validate->errors()
                ];
                return response()->json($respon, 401);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = $request->role;
            $user->school_id = $request->user()->school_id;
            $user->save();

            $email_data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'school' => School::where('id', $request->user()->school_id)->get(['name']),
            ];

            Mail::to($user->email, $user->name)->send(new Mailer($email_data));

            return response()->json([
                'success' => true,
                'message' => 'User register successfully',
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        } catch(\Exception $ex){
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        }
    }

    public function edit_user(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required',
                // 'email' => 'required|email',
                // 'password' => 'required|min:6',
                // 'role' => 'required|numeric|min:1|max:3',
            ]);

            if ($validate->fails()) {
                $respon = [
                    'success' => 'false',
                    'message' => $validate->errors()
                ];
                return response()->json($respon, 401);
            }

            $user = User::findOrFail($id);

            if($user) {
                $user->update([
                    'name' => $request->name,
                    // 'email' => $request->email,
                    // 'password' => $request->password,
                    // 'role' => $request->role,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'User Updated',
                    'data'    => $user  
                ], 200);

            }

            return response()->json([
                'success' => false,
                'message' => 'User Not Found',
            ], 404);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        } catch(\Exception $ex){
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        }

    }

    public function delete_user($id)
    {
        try {
            $user = User::findOrfail($id);

            if($user) {
                $user->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'User Deleted',
                ], 200);

            }

            return response()->json([
                'success' => false,
                'message' => 'User Not Found',
            ], 404);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        } catch(\Exception $ex){
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        }
    }
}
