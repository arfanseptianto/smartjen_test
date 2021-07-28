<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get_all_users(Request $request)
    {
        try {
            $user = User::where('school_id',$request->user()->school_id)->where('role', '>=', $request->user()->role)->orderBy('role')->orderBy('id')->get(['id', 'name', 'email', 'role', 'status']);

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
}
