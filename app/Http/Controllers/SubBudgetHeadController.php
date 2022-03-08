<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubBudgetHeadResource;
use App\Models\SubBudgetHead;
use App\Models\Department;
use App\Models\BudgetHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubBudgetHeadController extends Controller
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
        $subBudgetHeads = ! auth()->user()->isAdministrator ? SubBudgetHead::where('department_id', auth()->user()->department_id)->latest()->get() : SubBudgetHead::latest()->get();



        if ($subBudgetHeads->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => SubBudgetHeadResource::collection($subBudgetHeads),
            'status' => 'success',
            'message' => 'Sub-Budget List'
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
            'budget_head_id' => 'required|integer',
            'department_id' => 'required|integer',
            'budgetCode' => 'required|string|max:15|unique:sub_budget_heads',
            'name' => 'required|string',
            'description' => 'required',
            'type' => 'required|string|in:capital,recursive,personnel',
            'logisticsBudget' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'info',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $subBudgetHead = SubBudgetHead::create([
            'budget_head_id' => $request->budget_head_id,
            'department_id' => $request->department_id,
            'budgetCode' => $request->budgetCode,
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'description' => $request->description,
            'type' => $request->type,
            'logisticsBudget' => $request->logisticsBudget
        ]);

        return response()->json([
            'data' => new SubBudgetHeadResource($subBudgetHead),
            'status' => 'success',
            'message' => 'Sub-Budget Head has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubBudgetHead  $subBudgetHead
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($subBudgetHead)
    {
        $subBudgetHead = SubBudgetHead::find($subBudgetHead);

        if (! $subBudgetHead) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => new SubBudgetHeadResource($subBudgetHead),
            'status' => 'success',
            'message' => 'Sub-Budget Head details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubBudgetHead  $subBudgetHead
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($subBudgetHead)
    {
        $subBudgetHead = SubBudgetHead::find($subBudgetHead);

        if (! $subBudgetHead) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        return response()->json([
            'data' => new SubBudgetHeadResource($subBudgetHead),
            'status' => 'success',
            'message' => 'Sub-Budget Head details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubBudgetHead  $subBudgetHead
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $subBudgetHead)
    {
        $validator = Validator::make($request->all(), [
            'budget_head_id' => 'required|integer',
            'department_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'required',
            'type' => 'required|string|in:capital,recursive,personnel',
            'logisticsBudget' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'info',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $subBudgetHead = SubBudgetHead::find($subBudgetHead);

        if (! $subBudgetHead) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $subBudgetHead->update([
            'budget_head_id' => $request->budget_head_id,
            'department_id' => $request->department_id,
            'budgetCode' => $request->budgetCode,
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'description' => $request->description,
            'type' => $request->type,
            'logisticsBudget' => $request->logisticsBudget,
            // 'active' => $request->active
        ]);

        return response()->json([
            'data' => new SubBudgetHeadResource($subBudgetHead),
            'status' => 'success',
            'message' => 'Sub-Budget Head has been updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubBudgetHead  $subBudgetHead
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($subBudgetHead)
    {
        $subBudgetHead = SubBudgetHead::find($subBudgetHead);

        if (! $subBudgetHead) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID entered'
            ], 422);
        }

        $old = $subBudgetHead;

        $subBudgetHead->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Sub-Budget Head deleted successfully'
        ], 200);
    }
}
