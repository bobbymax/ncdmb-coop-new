<?php

namespace App\Http\Controllers;

use App\Http\Resources\BatchResource;
use App\Models\Approval;
use App\Models\Batch;
use App\Models\Expenditure;
use App\Models\SubBudgetHead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\QueriedBatchPayment;
use App\Mail\ClearedBatchPaymentQueryMail;
use Mail;

class BatchController extends Controller
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
        $batches = Batch::latest()->get();

        if ($batches->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => BatchResource::collection($batches),
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expenditures' => 'required|array',
            'amount' => 'required',
            'batch_no' => 'required|string|unique:batches',
            'noOfClaim' => 'required|integer',
            'subBudgetHeadCode' => 'required|string',
            'steps' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $batch = Batch::create([
            'user_id' => auth()->user()->id,
            'batch_no' => $request->batch_no,
            'amount' => $request->amount,
            'noOfClaim' => $request->noOfClaim,
            'subBudgetHeadCode' => $request->subBudgetHeadCode,
            'steps' => $request->steps,
            'level' => 'budget-office',
            'budget' => true
        ]);

        if ($batch) {
            foreach($request->expenditures as $value) {
                $expenditure = Expenditure::find($value['id']);

                if ($expenditure) {
                    $expenditure->batch_id = $batch->id;
                    $expenditure->status = "batched";
                    $expenditure->batched = true;
                    $expenditure->save();

                    $approval = new Approval;
                    $approval->user_id = auth()->user()->id;
                    $expenditure->approval()->save($approval);

                    if ($expenditure->claim_id > 0) {
                        $expenditure->claim->status = "batched";
                        $expenditure->claim->save();
                    }
                }
            }
        }

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch created successfully!'
        ], 201);
    }

    public function clearPayments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|string',
            'status' => 'required|string',
            'work_flow' => 'required|string',
            'batchId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors!'
            ], 500);
        }

        $batch = Batch::find($request->batchId);

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        if ($request->status === "queried") {
            $batch->status = $request->status;
            $batch->save();
        }

        $approval = new Approval;
        $approval->user_id = auth()->user()->id;
        $approval->work_flow = $request->work_flow;
        $approval->level = $request->level;
        $approval->description = $request->description;
        $approval->status = $request->status;
        $batch->approvals()->save($approval);

        $message = $this->processNextStep($batch, $approval->level, $approval->description);

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => $message
        ], 201);
    }

    protected function processNextStep(Batch $batch, $level, $description="")
    {
        // $batch = Batch::find($batchId);
        $subBudgetHead = SubBudgetHead::where('budgetCode', $batch->subBudgetHeadCode)->first();
        // $budgetYear = config('site.budget_year') ?? config('budget.budget_year');
        $message = "";

        switch ($level) {
            case 'treasury' :
                if ($batch->steps == 2) {
                    $batch->level = 'audit';
                    $batch->steps += 1;
                    $batch->treasury = false;
                    $batch->audit = true;
                    $batch->save();

                    $message = "Batch has been cleared by Treasury!!";
                } else {
                    $batch->editable = false;
                    $batch->closed = true;
                    $batch->status = 'paid';
                    $batch->save();

                    $subBudgetHead->getCurrentFund($this->getBudgetYear())->actual_expenditure += $batch->amount;
                    $subBudgetHead->getCurrentFund($this->getBudgetYear())->actual_balance -= $batch->amount;

                    $subBudgetHead->getCurrentFund($this->getBudgetYear())->save();

                    $message = "Batch payment has been posted by Treasury!!";
                }
                break;
            case 'audit' :
                if ($batch->status !== "queried") {
                    $batch->level = 'treasury';
                    $batch->steps = 4;
                    $batch->treasury = true;
                    $batch->audit = false;
                    $batch->save();
                    $message = "Batch has been cleared by Audit!!";
                } else {
                    $batch->queried = true;
                    if ($batch->save()) {
                        Mail::to($batch->initiator->email)->queue(new QueriedBatchPayment($batch, $description));
                    }
                }
                break;
            default :
                $batch->level = 'treasury';
                $batch->steps = 2;
                $batch->treasury = true;
                $batch->budget = false;
                $batch->editable = true;
                $batch->save();
                $message = "Batch has been cleared by Budget Office!!";
                break;
        }

        return $message;
    }

    public function clearBatchQuery($batchId)
    {
        $batch = Batch::find($batchId);

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        $batch->queried = false;
        if ($batch->save()) {
            Mail::to($batch->initiator->email)->queue(new ClearedBatchPaymentQueryMail($batch));
        }

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch query has now been cleared'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($batch)
    {
        $batch = Batch::where('batch_no', $batch)->first();

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch details'
        ], 200);
    }

    public function fetchBatchForReversal($batch)
    {
        // 1. Check for batch in database.
        $batch = Batch::where('batch_no', $batch)->first();
        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }
        // 2. Check if batch has been paid or closed!!
        if ($batch->status === "paid" || $batch->closed) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'This batch cannot be reversed because it has been marked as paid!!!'
            ], 200);
        }

        // 3. Check time of batch post.
        $now = Carbon::now('GMT+1');
        $dt = Carbon::parse($batch->created_at);

        if ($now->diffInHours($dt) >= 24) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'This batch can only be reversed by a budget officer!!'
            ], 200);
        }
        // 4. If 24 hours has passed - BCO can make reversal else Budget Officer can make reversal

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch Details!!'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($batch)
    {
        $batch = Batch::where('batch_no', $batch)->first();

        if (! $batch) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid ID selected'
            ], 422);
        }

        return response()->json([
            'data' => new BatchResource($batch),
            'status' => 'success',
            'message' => 'Batch details'
        ], 200);
    }

    protected function getBudgetYear()
    {
        return config('site.budget_year') ?? config('budget.budget_year');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Batch $batch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Batch $batch)
    {
        //
    }
}
