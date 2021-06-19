<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClaimController extends Controller
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
        $claims = auth()->user()->claims;

        if ($claims->count() < 1) {
            return response()->json([
                'data' => null,
                'status' => 'info',
                'message' => 'You do not have any claims registered!'
            ], 200);
        }

        return response()->json([
            'data' => $claims,
            'status' => 'success',
            'message' => 'List of registered claims'
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
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:staff-claim,touring-advance'
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
            'reference_no' => Str::random(8),
            'type' => $request->type
        ]);

        return response()->json([
            'data' => $claim,
            'status' => 'success',
            'message' => 'Claim has been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($claim)
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $claim,
            'status' => 'success',
            'message' => 'Claim details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($claim)
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => $claim,
            'status' => 'success',
            'message' => 'Claim details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $claim)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $claim->update([
            'title' => $request->title,
        ]);

        return response()->json([
            'data' => $claim,
            'status' => 'success',
            'message' => 'Claim has been updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Claim  $claim
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($claim)
    {
        $claim = Claim::find($claim);

        if (! $claim) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $claim->delete();

        return response()->json([
            'data' => null,
            'status' => 'success',
            'message' => 'Claim details deleted successfully'
        ], 200);
    }
}
