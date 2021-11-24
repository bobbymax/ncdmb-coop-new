<?php

namespace App\Http\Controllers;

use App\Http\Resources\RefundResource;
use App\Models\Expenditure;
use App\Models\Refund;
use App\Models\SubBudgetHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
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
        $refunds = Refund::latest()->get();

        if ($refunds->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No Data Found!!'
            ], 200);
        }

        return response()->json([
            'data' => RefundResource::collection($refunds),
            'status' => 'success',
            'message' => 'Refunds List'
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
            'expenditure_id' => 'required|integer',
            'department_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $expenditure = Expenditure::find($request->expenditure_id);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $refund = Refund::create([
            'expenditure_id' => $expenditure->id,
            'user_id' => auth()->user()->id,
            'department_id' => $request->department_id
        ]);

        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund request created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($refund)
    {
        $refund = Refund::find($refund);
        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'Invalid token entered'
            ], 422);
        }
        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($refund)
    {
        $refund = Refund::find($refund);
        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'Invalid token entered'
            ], 422);
        }
        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $refund)
    {
        $validator = Validator::make($request->all(), [
            'oldSubBudgetHead' => 'required|integer',
            'sub_budget_head_id' => 'required',
            'description' => 'required',
            'amount' => 'required',
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $oldSubBudget = SubBudgetHead::find($request->oldSubBudgetHead);
        $newSubBudget = SubBudgetHead::find($request->sub_budget_head_id);

        if (! $oldSubBudget || ! $newSubBudget) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $refund = Refund::find($refund);

        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $oldFund = $oldSubBudget->getCurrentFund('2021');
        $oldFund->actual_balance += $request->amount;
        $oldFund->save();

        $newFund = $newSubBudget->getCurrentFund('2021');
        $newFund->actual_balance -= $request->amount;
        $newFund->save();

        $refund->update([
            'sub_budget_head_id' => $newSubBudget->id,
            'description' => $request->description,
            'status' => $request->status,
            'closed' => true
        ]);

        return response()->json([
            'data' => new RefundResource($refund),
            'status' => 'success',
            'message' => 'Refund Details have been updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($refund)
    {
        $refund = Refund::find($refund);

        if (! $refund) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $old = $refund;
        $refund->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Refund deleted successfully!!'
        ], 200);
    }
}
