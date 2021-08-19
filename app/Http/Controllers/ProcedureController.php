<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProcedureResource;
use App\Models\Procedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcedureController extends Controller
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
        $procedures = Procedure::latest()->get();

        if ($procedures->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found!!!'
            ], 200);
        }

        return response()->json([
            'data' => ProcedureResource::collection($procedures),
            'status' => 'success',
            'message' => 'Workflow Lists'
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
           'work_flow_id' => 'required|integer',
           'role_id' => 'required|integer',
           'order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $procedure = Procedure::create([
            'work_flow_id' => $request->work_flow_id,
            'role_id' => $request->role_id,
            'order' => $request->order
        ]);

        return response()->json([
            'data' => new ProcedureResource($procedure),
            'status' => 'success',
            'message' => 'Procedure created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($procedure)
    {
        $procedure = Procedure::find($procedure);

        if (! $procedure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => new ProcedureResource($procedure),
            'status' => 'success',
            'message' => 'Procedure Details!!'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Procedure $procedure)
    {
        $procedure = Procedure::find($procedure);

        if (! $procedure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => new ProcedureResource($procedure),
            'status' => 'success',
            'message' => 'Procedure Details!!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $procedure)
    {
        $validator = Validator::make($request->all(), [
            'work_flow_id' => 'required|integer',
            'role_id' => 'required|integer',
            'order' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $procedure = Procedure::find($procedure);

        if (! $procedure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $procedure->update([
            'work_flow_id' => $request->work_flow_id,
            'role_id' => $request->role_id,
            'order' => $request->order
        ]);

        return response()->json([
            'data' => new ProcedureResource($procedure),
            'status' => 'success',
            'message' => 'Procedure created successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Procedure  $procedure
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($procedure)
    {
        $procedure = Procedure::find($procedure);

        if (! $procedure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $old = $procedure;
        $procedure->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Procedure Details!!'
        ], 200);
    }
}
