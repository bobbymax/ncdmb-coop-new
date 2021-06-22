<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $roles = Role::latest()->get();

        if ($roles->count() < 1) {
            return response()->json([
                'data' => null,
                'status' => 'info',
                'message' => 'No data found!'
            ], 200);
        }

        return response()->json([
            'data' => RoleResource::collection($roles),
            'status' => 'success',
            'message' => 'Roles list'
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_slots' => 'required|integer',
            'isSuper' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s)!:'
            ], 500);
        }

        $role = Role::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'max_slots' => $request->max_slots,
            'start_date' => Carbon::parse($request->start_date),
            'expiry_date' => $request->expiry_date != null ? Carbon::parse($request->expiry_date) : null,
            'isSuper' => $request->isSuper,
            'cannot_expire' => $request->cannot_expire
        ]);

        if ($request->has('permissions')) {
            foreach($request->permissions as $value) {
                $permission = Permission::where('label', $value)->first();

                if ($permission) {
                    $role->grantPermission($permission);
                }
            }
        }

        return response()->json([
            'data' => new RoleResource($role),
            'status' => 'success',
            'message' => 'Role has been created successfully!'
        ], 201);
    }

    public function show($role)
    {
        $role = Role::where('label', $role)->first();

        if (! $role) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid role id'
            ], 422);
        }

        return response()->json([
            'data' => new RoleResource($role),
            'status' => 'success',
            'message' => 'Role Details'
        ], 200);
    }

    public function edit($role)
    {
        $role = Role::where('label', $role)->first();

        if (! $role) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid role id'
            ], 422);
        }

        return response()->json([
            'data' => new RoleResource($role),
            'status' => 'success',
            'message' => 'Role Details'
        ], 200);
    }

    public function update(Request $request, $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_slots' => 'required|integer',
            'isSuper' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s)!:'
            ], 500);
        }
        $role = Role::find($role);

        if (! $role) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid role id'
            ], 422);
        }

        $role->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'max_slots' => $request->max_slots,
            'start_date' => Carbon::parse($request->start_date),
            'expiry_date' => $request->expiry_date != null ? Carbon::parse($request->expiry_date) : null,
            'isSuper' => $request->isSuper,
            'cannot_expire' => $request->cannot_expire
        ]);

        if ($request->has('permissions')) {
            foreach($request->permissions as $value) {
                $permission = Permission::where('label', $value)->first();

                if ($permission && ! in_array($permission->label, $role->permissions->pluck('label')->toArray())) {
                    $role->grantPermission($permission);
                }
            }
        }

        return response()->json([
            'data' => new RoleResource($role),
            'status' => 'success',
            'message' => 'Role updated successfully!'
        ], 200);
    }

    public function destroy($role)
    {
        $role = Role::where('label', $role)->first();

        if (! $role) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid role id'
            ], 422);
        }

        $old = $role;

        $role->delete();

        return response()->json([
            'data' => new RoleResource($old),
            'status' => 'success',
            'message' => 'Role deleted successfully!'
        ], 200);
    }
}
