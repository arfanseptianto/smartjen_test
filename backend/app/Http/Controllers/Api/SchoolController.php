<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function get_all_school(Request $request)
    {
        try {
            $school = School::orderBy('id')->get(['id', 'name']);

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
        } catch(\Exception $ex){
            return response()->json([
                'success' => false,
                'message' => substr($ex->getMessage(), 0, 97) . '...',
            ]);
        }
    }
}
