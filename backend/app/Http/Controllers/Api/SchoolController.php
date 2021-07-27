<?php

namespace App\Http\Controllers\Api;

// use Session;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SchoolController extends Controller
{
    /**
     * Get All Schools
     */
    public function get_all(Request $request)
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
                $ex->getMessage(),
            ]);
        }
    }
}
