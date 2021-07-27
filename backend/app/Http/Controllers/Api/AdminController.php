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
    /**
     * Get All Users
     */
    public function get_all_users(Request $request)
    {
        try {
            $user = User::first();
            Mail::to($user->email, $user->name)->send(new Mailer($user));
            
            $user = User::where('role','<>',1)->where('school_id',$request->user()->school_id)->orderBy('role')->orderBy('id')->get(['id', 'name', 'email', 'role', 'status']);

            //make response JSON
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
    
    /**
     * Add New User
     */
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

            $details = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'school' => School::where('id', $request->user()->school_id)->get(['name']),
            ];
     var_dump($details);
            Mail::to($user->email, $user->name)->send(new Mailer($details));

            return response()->json([
                'success' => true,
                'message' => 'User register successfully',
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        } catch(\Exception $e){
            echo "Email gagal dikirim karena $e.";
        }
    }
    
    /**
     * Edit User
     */
    public function edit_user(Request $request)
    {
        try {
            //get data from table posts
            $school = School::orderBy('id')->get(['id', 'name', 'address', 'phone']);
    
            //make response JSON
            return response()->json([
                'success' => true,
                'message' => 'List All School',
                'total'   => count($school),
                'data'    => $school,
            ], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        }
    }
    
    /**
     * Delete User
     */
    public function delete_user(Request $request)
    {
        try {
            //get data from table posts
            $school = School::orderBy('id')->get(['id', 'name', 'address', 'phone']);
    
            //make response JSON
            return response()->json([
                'success' => true,
                'message' => 'List All School',
                'total'   => count($school),
                'data'    => $school,
            ], 200);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        }
    }
}
