<?php

namespace App\Http\Controllers;

use App\Http\Resources\BatchResource;
use App\Http\Resources\ExpenditureResource;
use App\Models\Expenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenditureController extends Controller
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
        $expenditures = Expenditure::all();

        if ($expenditures->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!!'
            ], 200);
        }

        return response()->json([
            'data' => ExpenditureResource::collection($expenditures),
            'status' => 'success',
            'message' => 'Expenditure List'
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
            'sub_budget_head_id' => 'required|integer',
            'claim_id' => 'required',
            'status' => 'required|string|in:cleared,batched,queried,paid',
            'beneficiary' => 'required|string',
            'description' => 'required',
            'payment_type' => 'required|string|in:staff-payment,third-party',
            'type' => 'string|in:staff-claim,touring-advance,other',
            'amount' => 'required',
            'new_balance' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $budgetYear = config('site.budget_year') ?? config('budget.budget_year');

        $expenditure = Expenditure::create([
            'sub_budget_head_id' => $request->sub_budget_head_id,
            'claim_id' => $request->claim_id,
            'user_id' => auth()->user()->id,
            'type' => $request->type,
            'payment_type' => $request->payment_type,
            'beneficiary' => $request->beneficiary,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => $request->status,
            'additional_info' => $request->additional_info
        ]);

        if ($expenditure && $expenditure->subBudgetHead($budgetYear) !== null) {
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_expenditure += $expenditure->amount;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_balance -= $expenditure->amount;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->save();

            if ($expenditure->claim_id > 0) {
                $expenditure->claim->status = "cleared";
                $expenditure->claim->save();
            }
        }

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expenditure  $expenditure
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($expenditure)
    {
        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expenditure  $expenditure
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($expenditure)
    {
        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expenditure  $expenditure
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $expenditure)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $expenditure = Expenditure::find($expenditure);
        $budgetYear = config('site.budget_year') ?? config('budget.budget_year');

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $previousAmount = $expenditure->amount;

        $expenditure->update([
            'amount' => $request->amount,
        ]);

        if ($previousAmount > $request->amount) {
            $diff = $previousAmount - $request->amount;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_expenditure -= $diff;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_balance += $diff;
        } else {
            $diff = $request->amount - $previousAmount;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_expenditure += $diff;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_balance -= $diff;
        }

        $expenditure->subBudgetHead->getCurrentFund($budgetYear)->save();

        return response()->json([
            'data' => new ExpenditureResource($expenditure),
            'status' => 'success',
            'message' => 'Expenditure has been updated successfully!'
        ], 200);
    }

    public function batchExpenditureUpdate(Request $request, $expenditure)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $previousAmount = $expenditure->amount;
        $budgetYear = config('site.budget_year') ?? config('budget.budget_year');

        $expenditure->update([
            'amount' => $request->amount,
        ]);

        if ($previousAmount > $request->amount) {
            $diff = $previousAmount - $request->amount;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_expenditure -= $diff;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_balance += $diff;
        } else {
            $diff = $request->amount - $previousAmount;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_expenditure += $diff;
            $expenditure->subBudgetHead->getCurrentFund($budgetYear)->booked_balance -= $diff;
        }

        $expenditure->subBudgetHead->getCurrentFund($budgetYear)->save();

        $expenditure->batch->amount = $expenditure->batch->expenditures->sum('amount');
        $expenditure->batch->save();

        return response()->json([
            'data' => new BatchResource($expenditure->batch),
            'status' => 'success',
            'message' => 'Expenditure has been updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expenditure  $expenditure
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($expenditure)
    {
        $expenditure = Expenditure::find($expenditure);

        if (! $expenditure) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        if ($expenditure->batch_id != 0) {
            return response()->json([
                'data' => $expenditure,
                'status' => 'error',
                'message' => 'You cannot delete an expenditure that has been batched already!'
            ], 422);
        }

        $booked = $expenditure->subBudgetHead->fund->booked_expenditure - $expenditure->amount;

        $expenditure->subBudgetHead->fund->booked_expenditure -= $expenditure->amount;
        $expenditure->subBudgetHead->fund->booked_balance = $expenditure->subBudgetHead->fund->approved_amount - $booked;
        $expenditure->subBudgetHead->fund->save();

        $old = $expenditure;
        $expenditure->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Expenditure has been deleted successfully!'
        ], 200);
    }
}
