<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    protected $currentDepts = [];

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
        $staff = User::all();

        if ($staff->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found!'
            ], 404);
        }

        return response()->json([
            'data' => UserResource::collection($staff),
            'status' => 'success',
            'message' => 'List of Staffs'
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
           'staff_no' => 'required|string|unique:users',
           'grade_level_id' => 'required|integer',
           'departments' => 'required',
           'name' => 'required|string',
           'email' => 'required|email|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'errors',
                'message' => 'Please fix the following errors!!'
            ], 500);
        }

        $staff = User::create([
            'staff_no' => $request->staff_no,
            'grade_level_id' => $request->grade_level_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('Password1')
        ]);

        if ($request->has('departments')) {
            foreach($request->departments as $department) {
                $dept = Department::find($department['value']);

                if ($dept) {
                    $staff->departments()->save($dept);
                }
            }
        }

        if ($request->has('roles')) {
            foreach($request->roles as $role) {
                $r = Role::find($role['value']);

                if ($r) {
                    $staff->roles()->save($r);
                }
            }
        }

        return response()->json([
            'data' => new UserResource($staff),
            'status' => 'success',
            'message' => 'Staff record created successfully!!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($user)
    {
        $user = User::find($user);

        if(! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => new UserResource($user),
            'status' => 'success',
            'message' => 'User Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($user)
    {
        $user = User::find($user);

        if(! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        return response()->json([
            'data' => new UserResource($user),
            'status' => 'success',
            'message' => 'User Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $user)
    {
        $validator = Validator::make($request->all(), [
            'staff_no' => 'required|string',
            'grade_level_id' => 'required|integer',
            'departments' => 'required',
            'name' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'errors',
                'message' => 'Please fix the following errors!!'
            ], 500);
        }

        $staff = User::find($user);

        if (! $staff) {
            return response()->json([
                'data' => null,
                'status' => 'warning',
                'message' => 'Invalid token entered!'
            ], 422);
        }

        $staff->update([
            'staff_no' => $request->staff_no,
            'grade_level_id' => $request->grade_level_id,
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->has('departments')) {
            foreach($request->departments as $department) {
                $dept = Department::find($department['value']);

                if ($dept && !in_array($dept->id, $staff->currentDepartments())) {
                    $staff->departments()->save($dept);
                }
            }
        }

        if ($request->has('roles')) {
            foreach($request->roles as $role) {
                $r = Role::find($role['value']);

                if ($r && ! in_array($r->id, $staff->currentRoles())) {
                    $staff->roles()->save($r);
                }
            }
        }

        return response()->json([
            'data' => new UserResource($staff),
            'status' => 'success',
            'message' => 'Staff record updated successfully!!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($user)
    {
        $user = User::find($user);

        if(! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token entered'
            ], 422);
        }

        $old = $user;
        $user->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'User Details'
        ], 200);
    }
}
