<?php

namespace App\Http\Controllers;

use App\Models\TouringAdvance;
use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TouringAdvanceResource;
use Carbon\Carbon;

class TouringAdvanceController extends Controller
{

    protected $touring;

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
        $touringAdvances = TouringAdvance::latest()->get();

        if ($touringAdvances->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => TouringAdvanceResource::collection($touringAdvances),
            'status' => 'success',
            'message' => 'Batches list'
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
            'reference_no' => 'required|string|unique:touring_advances',
            'start_date' => 'required',
            'end_date' => 'required',
            'amount' => 'required',
            'title' => 'required',
            'status' => 'required|string|in:pending,raised,rettired',
            'type' => 'required|string|in:touring-advance'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $claim = Claim::create([
            'title' => $request->title,
            'reference_no' => $request->reference_no,
            'type' => $request->type,
            'user_id' => $request->user_id,
            'total_amount' => $request->amount,
            'status' => 'registered'
        ]);

        if ($claim) {
            $this->touring = TouringAdvance::create([
                'reference_no' => $claim->reference_no,
                'claim_id' => $claim->id,
                'start_date' => Carbon::parse($request->start_date),
                'end_date' => Carbon::parse($request->end_date),
                'status' => $request->status,
                'user_id' => auth()->user()->id
            ]);
        }

        return response()->json([
            'data' => new TouringAdvanceResource($this->touring),
            'status' => 'success',
            'message' => 'Touring Advance has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TouringAdvance  $touringAdvance
     * @return \Illuminate\Http\Response
     */
    public function show($touringAdvance)
    {
        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => new TouringAdvanceResource($touringAdvance),
            'status' => 'success',
            'message' => 'Touring Advance Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TouringAdvance  $touringAdvance
     * @return \Illuminate\Http\Response
     */
    public function edit($touringAdvance)
    {
        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => new TouringAdvanceResource($touringAdvance),
            'status' => 'success',
            'message' => 'Touring Advance Details'
        ], 200);
    }

    public function changeTouringAdvanceStatus(Request $request, $touringAdvance)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,raised,rettired',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $touringAdvance->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'data' => new TouringAdvanceResource($touringAdvance),
            'status' => 'success',
            'message' => 'Touring Advance Payment has been raised successfully!!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TouringAdvance  $touringAdvance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $touringAdvance)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'amount' => 'required',
            'title' => 'required',
            'status' => 'required|string|in:pending,raised,rettired',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $touringAdvance->claim->update([
            'title' => $request->title,
            'user_id' => $request->user_id,
            'total_amount' => $request->amount,
        ]);

        $touringAdvance->update([
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'status' => $request->status,
        ]);

        return response()->json([
            'data' => new TouringAdvanceResource($touringAdvance),
            'status' => 'success',
            'message' => 'Touring Advance Details updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TouringAdvance  $touringAdvance
     * @return \Illuminate\Http\Response
     */
    public function destroy($touringAdvance)
    {
        $touringAdvance = TouringAdvance::find($touringAdvance);

        if (! $touringAdvance) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $old = $touringAdvance;

        $touringAdvance->claim->delete();
        $touringAdvance->delete();

        return response()->json([
            'data' => new TouringAdvanceResource($old),
            'status' => 'success',
            'message' => 'Touring Advance Details deleted successfully!!'
        ], 200);
    }
}
