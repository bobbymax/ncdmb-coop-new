<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Instruction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstructionController extends Controller
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
    public function index($claim)
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token chosen'
            ], 422);
        }

        if ($claim->instructions->count() < 1) {
            return response()->json([
                'data' => null,
                'status' => 'info',
                'message' => 'Add new instructions to this claim'
            ], 200);
        }

        return response()->json([
            'data' => $claim->instructions,
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
            'amount' => 'required|integer',
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
        $instruction->additional_benefit = isset($request->additional_benefit) ? $request->additional_benefit : null;
        $instruction->from = Carbon::parse($request->from);
        $instruction->to = Carbon::parse($request->to);
        $instruction->description = $request->description;
        $instruction->amount = $request->amount;

        $claim->instructions()->save($instruction);

        return response()->json([
            'data' => $claim->instructions,
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
            'data' => $claim->instructions,
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
            'data' => $claim->instructions,
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
            'amount' => 'required|integer',
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
        $instruction->additional_benefit = isset($request->additional_benefit) ? $request->additional_benefit : null;
        $instruction->from = Carbon::parse($request->from);
        $instruction->to = Carbon::parse($request->to);
        $instruction->description = $request->description;
        $instruction->amount = $request->amount;

        $claim->instructions()->save($instruction);

        return response()->json([
            'data' => $claim->instructions,
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

        $instruction->delete();

        return response()->json([
            'data' => $claim->instructions,
            'status' => 'success',
            'message' => 'Instruction details deleted successfully!'
        ], 200);
    }
}
