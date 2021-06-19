<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GradeLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $gradeLevels = GradeLevel::all();

        if ($gradeLevels->count() < 1) {
            return response()->json([
                'data' => null,
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => $gradeLevels,
            'status' => 'success',
            'message' => 'Grade Level List'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:grade_levels',
            'code' => 'required|string|max:3|unique:grade_levels'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s)'
            ], 500);
        }

        $gradeLevel = GradeLevel::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code
        ]);

        return response()->json([
            'data' => $gradeLevel,
            'status' => 'success',
            'message' => 'Grade level has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GradeLevel  $gradeLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($gradeLevel)
    {
        $gradeLevel = GradeLevel::find($gradeLevel);

        if (! $gradeLevel) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $gradeLevel,
            'status' => 'success',
            'message' => 'Grade Level details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GradeLevel  $gradeLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($gradeLevel)
    {
        $gradeLevel = GradeLevel::find($gradeLevel);

        if (! $gradeLevel) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $gradeLevel,
            'status' => 'success',
            'message' => 'Grade Level details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GradeLevel  $gradeLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $gradeLevel)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s)'
            ], 500);
        }

        $gradeLevel = GradeLevel::find($gradeLevel);

        if (! $gradeLevel) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $gradeLevel->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'code' => $request->code
        ]);

        return response()->json([
            'data' => $gradeLevel,
            'status' => 'success',
            'message' => 'Grade level has been updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GradeLevel  $gradeLevel
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($gradeLevel)
    {
        $gradeLevel = GradeLevel::find($gradeLevel);

        if (! $gradeLevel) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $gradeLevel->delete();

        return response()->json([
            'data' => null,
            'status' => 'success',
            'message' => 'Grade Level deleted successfully!'
        ], 200);
    }
}
