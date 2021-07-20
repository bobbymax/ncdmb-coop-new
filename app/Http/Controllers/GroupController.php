<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GroupController extends Controller
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
        $groups = Group::all();

        if ($groups->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => GroupResource::collection($groups),
            'status' => 'success',
            'message' => 'Group List'
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
            'name' => 'required|string|max:255',
            'max_slots' => 'required|integer',
            'start_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $group = Group::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'max_slots' => $request->max_slots,
            'start_date' => Carbon::parse($request->start_date),
            'expiry_date' => $request->expiry_date != null ? Carbon::parse($request->expiry_date) : null,
            'cannot_expire' => $request->cannot_expire,
        ]);

        return response()->json([
            'data' => new GroupResource($group),
            'status' => 'success',
            'message' => 'Group details have been created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($group)
    {
        $group = Group::find($group);

        if (! $group) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entry'
            ], 422);
        }

        return response()->json([
            'data' => new GroupResource($group),
            'status' => 'success',
            'message' => 'Group details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($group)
    {
        $group = Group::find($group);

        if (! $group) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entry'
            ], 422);
        }

        return response()->json([
            'data' => new GroupResource($group),
            'status' => 'success',
            'message' => 'Group details'
        ], 200);
    }

    public function addStaffsToGroup(Request $request, $group)
    {
        $validator = Validator::make($request->all(), [
            'staffs' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the error'
            ], 500);
        }

        $group = Group::find($group);

        if (! $group) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid group ID'
            ], 422);
        }

        foreach($request->staffs as $value) {
            $staff = User::find($value);

            if ($staff && ! in_array($staff->id, $group->staffs->pluck('id')->toArray())) {
                $group->addStaff($staff);
            }
        }

        return response()->json([
            'data' => new GroupResource($group),
            'status' => 'success',
            'message' => 'Staffs have been added to this group successfully!'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $group)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_slots' => 'required|integer',
            'start_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following errors'
            ], 500);
        }

        $group = Group::find($group);

        if (! $group) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entry'
            ], 422);
        }

        $group->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'max_slots' => $request->max_slots,
            'start_date' => Carbon::parse($request->start_date),
            'expiry_date' => $request->expiry_date != null ? Carbon::parse($request->expiry_date) : null,
            'cannot_expire' => $request->cannot_expire,
        ]);

        return response()->json([
            'data' => new GroupResource($group),
            'status' => 'success',
            'message' => 'Group details have been updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($group)
    {
        $group = Group::find($group);

        if (! $group) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entry'
            ], 422);
        }

        $old = $group;
        $group->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Group details have been deleted successfully!'
        ], 200);
    }
}
