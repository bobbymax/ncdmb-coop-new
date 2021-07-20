<?php

namespace App\Http\Controllers;

use App\Http\Resources\BenefitResource;
use App\Http\Resources\EntitlementResource;
use App\Models\Benefit;
use App\Models\Entitlement;
use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntitlementController extends Controller
{
    protected $loadedEntitlements = [];
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
        $entitlements = Entitlement::all();

        if ($entitlements->count() < 1) {
            return response()->json([
                'data' => null,
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => EntitlementResource::collection($entitlements),
            'status' => 'success',
            'message' => 'Entitlements list'
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

    public function getEntitlementDependencies()
    {
        $gradeLevels = GradeLevel::all();
        $benefits = Benefit::all();

        $results = compact('gradeLevels', 'benefits');

        return response()->json([
            'data' => $results,
            'status' => 'success',
            'message' => 'Dependencies fetched successfully!'
        ], 200);
    }

    public function saveBatchEntitlements(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'benefit_id' => 'required|integer',
            'price_list_id' => 'required|integer',
            'grades' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $benefit = Benefit::find($request->benefit_id);

        if(! $benefit) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        if ($request->has('grades')) {
            foreach($request->grades as $grade) {
                $entitlement = Entitlement::create([
                    'grade_level_id' => $grade['value'],
                    'benefit_id' => $benefit->id,
                    'price_list_id' => $request->price_list_id
                ]);

                $this->loadedEntitlements[] = $entitlement;
            }
        }

        // $entitlements = Entitlement::all();

        return response()->json([
            'data' => new BenefitResource($benefit),
            'status' => 'success',
            'message' => 'Entitlement created successfully!'
        ], 201);
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
            'grade_level_id' => 'required|integer',
            'benefit_id' => 'required|integer',
            'amount' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $entitlement = Entitlement::create([
            'grade_level_id' => $request->grade_level_id,
            'benefit_id' => $request->benefit_id,
            'amount' => $request->amount
        ]);

        return response()->json([
            'data' => new EntitlementResource($entitlement),
            'status' => 'success',
            'message' => 'Entitlement created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Entitlement  $entitlement
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($entitlement)
    {
        $entitlement = Entitlement::find($entitlement);

        if (! $entitlement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered!'
            ], 422);
        }

        return response()->json([
            'data' => new EntitlementResource($entitlement),
            'status' => 'success',
            'message' => 'Entitlement list'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Entitlement  $entitlement
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($entitlement)
    {
        $entitlement = Entitlement::find($entitlement);

        if (! $entitlement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered!'
            ], 422);
        }

        return response()->json([
            'data' => new EntitlementResource($entitlement),
            'status' => 'success',
            'message' => 'Entitlement list'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Entitlement  $entitlement
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $entitlement)
    {
        $validator = Validator::make($request->all(), [
            'grade_level_id' => 'required|integer',
            'benefit_id' => 'required|integer',
            'amount' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $entitlement = Entitlement::find($entitlement);

        if (! $entitlement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered!'
            ], 422);
        }

        $entitlement->update([
            'grade_level_id' => $request->grade_level_id,
            'benefit_id' => $request->benefit_id,
            'amount' => $request->amount
        ]);

        return response()->json([
            'data' => new EntitlementResource($entitlement),
            'status' => 'success',
            'message' => 'Entitlement updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Entitlement  $entitlement
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($entitlement)
    {
        $entitlement = Entitlement::find($entitlement);

        if (! $entitlement) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered!'
            ], 422);
        }

        $old = $entitlement;
        $entitlement->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Entitlement deleted successfully!'
        ], 200);
    }
}
