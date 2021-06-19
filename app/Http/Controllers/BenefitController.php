<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BenefitController extends Controller
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
        $benefits = Benefit::all();

        if ($benefits->count() < 1) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'No data found!!'
            ], 200);
        }

        return response()->json([
            'data' => $benefits,
            'status' => 'success',
            'message' => 'Benefit List'
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
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $benefit = Benefit::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'description' => $request->description
        ]);

        return response()->json([
            'data' => $benefit,
            'status' => 'success',
            'message' => 'Benefit created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Benefit  $benefit
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($benefit)
    {
        $benefit = Benefit::find($benefit);

        if (! $benefit) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $benefit,
            'status' => 'success',
            'message' => 'Benefit details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Benefit  $benefit
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($benefit)
    {
        $benefit = Benefit::find($benefit);

        if (! $benefit) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $benefit,
            'status' => 'success',
            'message' => 'Benefit details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Benefit  $benefit
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $benefit)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'notActive' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $benefit = Benefit::find($benefit);

        if (! $benefit) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $benefit->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'description' => $request->description,
            'notActive' => $request->notActive
        ]);

        return response()->json([
            'data' => $benefit,
            'status' => 'success',
            'message' => 'Benefit updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Benefit  $benefit
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($benefit)
    {
        $benefit = Benefit::find($benefit);

        if (! $benefit) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $benefit->delete();

        return response()->json([
            'data' => null,
            'status' => 'success',
            'message' => 'Benefit deleted successfully!'
        ], 200);
    }
}
