<?php

namespace App\Http\Controllers;

use App\Http\Resources\InstructionResource;
use App\Http\Resources\ClaimResource;
use App\Models\Claim;
use App\Models\Instruction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstructionController extends Controller
{

    private $total = 0;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($claim)
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => [],
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        if ($claim->instructions->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'Add new instructions to this claim'
            ], 200);
        }

        return response()->json([
            'data' => InstructionResource::collection($claim->instructions),
            'status' => 'success',
            'message' => 'Instruction details list'
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
    public function store(Request $request, $claim)
    {
        $validator = Validator::make($request->all(), [
            'benefit_id' => 'required|integer',
            'from' => 'required|date',
            'to' => 'required|date',
            'description' => 'required|min:3',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        $instruction = new Instruction;

        $instruction->benefit_id = $request->benefit_id;
        $instruction->additional_benefit_id = $request->additional_benefit_id;
        $instruction->from = Carbon::parse($request->from);
        $instruction->to = Carbon::parse($request->to);
        $instruction->description = $request->description;
        $instruction->amount = $request->amount;

        $claim->instructions()->save($instruction);

        return response()->json([
            'data' => new InstructionResource($instruction),
            'status' => 'success',
            'message' => 'Instruction details created successfully!'
        ], 201);
    }

    public function addClaimInstructions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'claim_id' => 'required|integer',
            'instructions' => 'required|array',
            'status' => 'required|in:registered,unregistered'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $claim = Claim::find($request->claim_id);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        if ($request->has('instructions')) {

            foreach ($request->instructions as $value) {
                $instruction = new Instruction;

                $instruction->benefit_id = $value['benefit_id'];
                $instruction->additional_benefit_id = $value['additional_benefit_id'];
                $instruction->from = Carbon::parse($value['from']);
                $instruction->to = Carbon::parse($value['to']);
                $instruction->description = $value['description'];
                $instruction->amount = $value['amount'];

                $claim->instructions()->save($instruction);

                $this->total += $instruction->amount;
            }
        }

        $claim->total_amount = $this->total;
        $claim->status = $request->status;
        $claim->save();

        return response()->json([
            'data' => new ClaimResource($claim),
            'status' => 'success',
            'message' => 'Instruction details created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Instruction  $instruction
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($claim, $instruction)
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        $instruction = Instruction::find($instruction);

        if (! $instruction) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        return response()->json([
            'data' => new InstructionResource($instruction),
            'status' => 'success',
            'message' => 'Instruction details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $claim
     * @param \App\Models\Instruction $instruction
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($claim, $instruction)
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        $instruction = Instruction::find($instruction);

        if (! $instruction) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        return response()->json([
            'data' => new InstructionResource($instruction),
            'status' => 'success',
            'message' => 'Instruction details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Instruction  $instruction
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $claim, $instruction)
    {
        $validator = Validator::make($request->all(), [
            'benefit_id' => 'required|integer',
            'from' => 'required|date',
            'to' => 'required|date',
            'description' => 'required|min:3',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        $instruction = Instruction::find($instruction);

        if (! $instruction) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        $instruction->benefit_id = $request->benefit_id;
        $instruction->additional_benefit_id = $request->category;
        $instruction->from = Carbon::parse($request->from);
        $instruction->to = Carbon::parse($request->to);
        $instruction->description = $request->description;
        $instruction->amount = $request->amount;

        $claim->instructions()->save($instruction);

        return response()->json([
            'data' => new InstructionResource($instruction),
            'status' => 'success',
            'message' => 'Instruction details updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Instruction  $instruction
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($claim, $instruction)
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        $instruction = Instruction::find($instruction);

        if (! $instruction) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        $old = $instruction;
        $instruction->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Instruction details deleted successfully!'
        ], 200);
    }
}
