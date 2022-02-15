<?php

namespace App\Http\Controllers;

use App\Models\Logistics;
use App\Http\Resources\LogisticsResource;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{

    public function __construct() 
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logisticsRequests = Logistics::latest()->get();

        if ($logisticsRequests->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => LogisticsResource::collection($logisticsRequests),
            'status' => 'success',
            'message' => 'Logistics List'
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'department_id' => 'required|integer',
            'sub_budget_head_id' => 'required|integer',
            'description' => 'required|string|min:3',
            'amount' => 'required',
            'status' => 'required|string|in:pending,approved,denied',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $logisticsRequest = Logistics::create([
            'controller_id' => auth()->user()->id,
            'user_id' => $request->user_id,
            'department_id' => $request->department_id,
            'sub_budget_head_id' => $request->sub_budget_head_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => $request->status
        ]);

        return response()->json([
            'data' => new LogisticsResource($logisticsRequest),
            'status' => 'success',
            'message' => 'Logistics Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Logistics  $logistics
     * @return \Illuminate\Http\Response
     */
    public function show($logisticsRequest)
    {
        $logisticsRequest = Logistics::find($logisticsRequest);

        if (! $logisticsRequest) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new LogisticsResource($logisticsRequest),
            'status' => 'success',
            'message' => 'Logistics Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Logistics  $logistics
     * @return \Illuminate\Http\Response
     */
    public function edit($logisticsRequest)
    {
        $logisticsRequest = Logistics::find($logisticsRequest);

        if (! $logisticsRequest) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new LogisticsResource($logisticsRequest),
            'status' => 'success',
            'message' => 'Logistics Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Logistics  $logistics
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $logisticsRequest)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'department_id' => 'required|integer',
            'sub_budget_head_id' => 'required|integer',
            'description' => 'required|string|min:3',
            'amount' => 'required',
            'status' => 'required|string|in:pending,approved,denied',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $logisticsRequest = Logistics::find($logisticsRequest);

        if (! $logisticsRequest) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $logisticsRequest->update([
            'user_id' => $request->user_id,
            'department_id' => $request->department_id,
            'sub_budget_head_id' => $request->sub_budget_head_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => $request->status
        ]);

        return response()->json([
            'data' => new LogisticsResource($logisticsRequest),
            'status' => 'success',
            'message' => 'Logistics Updated Successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Logistics  $logistics
     * @return \Illuminate\Http\Response
     */
    public function destroy($logisticsRequest)
    {
        $logisticsRequest = Logistics::find($logisticsRequest);

        if (! $logisticsRequest) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $logisticsRequest;
        $logisticsRequest->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Logistics Deleted Successfully!'
        ], 200); 
    }
}
