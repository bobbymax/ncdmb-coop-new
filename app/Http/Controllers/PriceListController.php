<?php

namespace App\Http\Controllers;

use App\Http\Resources\BenefitResource;
use App\Http\Resources\PriceListResource;
use App\Models\Benefit;
use App\Models\PriceList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PriceListController extends Controller
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
        $prices = PriceList::all();

        if ($prices->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!!'
            ], 200);
        }

        return response()->json([
            'data' => PriceListResource::collection($prices),
            'status' => 'success',
            'message' => 'Price Lists'
        ], 200);
    }

    public function getDependencies()
    {
        $benefits = Benefit::all();

        return response()->json([
            'data' => BenefitResource::collection($benefits),
            'status' => 'success',
            'message' => 'Dependencies'
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

        $benefit = Benefit::find($request->benefit_id);

        if(! $benefit) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $exists = $benefit->prices->where('amount', $request->amount)->first();

        if ($exists) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'Price already exists for this benefit!!'
            ], 422);
        }

        $price = PriceList::create([
            'benefit_id' => $benefit->id,
            'amount' => $request->amount
        ]);

        return response()->json([
            'data' => new PriceListResource($price),
            'status' => 'success',
            'message' => 'Entitlement created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PriceList  $priceList
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($priceList)
    {
        $price = PriceList::find($priceList);

        if(! $price) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new PriceListResource($price),
            'status' => 'success',
            'message' => 'Price Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PriceList  $priceList
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($priceList)
    {
        $price = PriceList::find($priceList);

        if(! $price) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new PriceListResource($price),
            'status' => 'success',
            'message' => 'Price Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PriceList  $priceList
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $priceList)
    {
        $validator = Validator::make($request->all(), [
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

        $price = PriceList::find($priceList);
        $benefit = Benefit::find($request->benefit_id);

        if(! $price || ! $benefit) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $price->update([
            'benefit_id' => $request->benefit_id,
            'amount' => $request->amount
        ]);

        return response()->json([
            'data' => new PriceListResource($price),
            'status' => 'success',
            'message' => 'Price updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PriceList  $priceList
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($priceList)
    {
        $price = PriceList::find($priceList);

        if(! $price) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $price;
        $price->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Price deleted successfully!'
        ], 200);
    }
}
