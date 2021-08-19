<?php

namespace App\Http\Controllers;

use App\Models\WorkFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WorkFlowController extends Controller
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
        $workflows = WorkFlow::latest()->get();

        if ($workflows->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found!!!'
            ], 200);
        }

        return response()->json([
            'data' => $workflows,
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
           'name' => 'required|string',
           'rule' => 'required|string|in:sequence,broadcast',
           'active' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $workflow = WorkFlow::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'rule' => $request->rule,
            'active' => $request->active
        ]);

        return response()->json([
            'data' => $workflow,
            'status' => 'success',
            'message' => 'Workflow created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkFlow  $workFlow
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($workFlow)
    {
        $workflow = WorkFlow::find($workFlow);

        if (! $workflow) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $workflow,
            'status' => 'success',
            'message' => 'Workflow details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkFlow  $workFlow
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($workFlow)
    {
        $workflow = WorkFlow::find($workFlow);

        if (! $workflow) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $workflow,
            'status' => 'success',
            'message' => 'Workflow details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkFlow  $workFlow
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $workFlow)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'rule' => 'required|string|in:sequence,broadcast',
            'active' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors:'
            ], 500);
        }

        $workflow = WorkFlow::find($workFlow);

        if (! $workflow) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $workflow->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'rule' => $request->rule,
            'active' => $request->active
        ]);

        return response()->json([
            'data' => $workflow,
            'status' => 'success',
            'message' => 'Workflow updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkFlow  $workFlow
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($workFlow)
    {
        $workflow = WorkFlow::find($workFlow);

        if (! $workflow) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $old = $workflow;
        $workflow->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Workflow details'
        ], 200);
    }
}
