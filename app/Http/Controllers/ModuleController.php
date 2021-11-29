<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModuleResource;
use App\Models\Department;
use App\Models\Group;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $modules = Module::latest()->get();

        if ($modules->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => ModuleResource::collection($modules),
            'status' => 'success',
            'message' => 'Modules List'
        ], 200);
    }

    public function fetchApplications()
    {
        $modules = Module::where('type', 'application')->latest()->get();

        if ($modules->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 200);
        }

        return response()->json([
            'data' => ModuleResource::collection($modules),
            'status' => 'success',
            'message' => 'Modules List'
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'path' => 'required|string',
            'component' => 'required|string|unique:modules',
            'isAuthRequired' => 'required',
            'generatePermissions' => 'required',
            'parentId' => 'required',
            'isAdministration' => 'required',
            'type' => 'required|string|in:application,module,page'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $module = Module::create([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'path' => $request->path,
            'component' => $request->component,
            'icon' => $request->icon,
            'parentId' => $request->parentId,
            'generatePermissions' => $request->generatePermissions,
            'isAuthRequired' => $request->isAuthRequired,
            'isMenu' => $request->isMenu,
            'isAdministration' => $request->isAdministration,
            'type' => $request->type
        ]);

        if ($request->generatePermissions) {

            foreach ($module->normalizer($module->name) as $value) {
                $permission = $module->savePermission($value, $module->name);

                if ($permission != null) {
                    $module->addPermission($permission);
                }
            }

        }

        if ($request->has('departments')) {
            $currentDepartments = $module->departments->pluck('id')->toArray();

            foreach($request->departments as $department) {
                $dept = Department::find($department['value']);

                if ($dept && ! in_array($dept->id, $currentDepartments)) {
                    $module->departments()->save($dept);
                }
            }
        }

        if ($request->has('roles')) {
            $currentRoles = $module->roles->pluck('id')->toArray();

            foreach($request->roles as $role) {
                $r = Role::find($role['value']);

                if ($r && ! in_array($r->id, $currentRoles)) {
                    $module->roles()->save($r);
                }
            }
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module Created Successfully!'
        ], 201);
    }

    public function edit($module)
    {
        $module = Module::where('label', $module)->first();

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module Details'
        ], 200);
    }

    public function show($module)
    {
        $module = Module::where('label', $module)->first();

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module Details'
        ], 200);
    }

    public function addRolesToModule(Request $request, $module)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $module = Module::find($module);

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        foreach ($request->roles as $role) {
            $role = Role::find($role);

            if ($role && ! in_array($role->id, $module->roles->pluck('id')->toArray())) {
                $module->addRole($role);
            }
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module updated successfully!'
        ], 200);
    }

    public function grantGroupsAccess(Request $request, $module)
    {
        $validator = Validator::make($request->all(), [
            'groups' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $module = Module::find($module);

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        foreach ($request->groups as $value) {
            $group = Group::find($value);

            if ($group && ! in_array($group->id, $module->groups->pluck('id')->toArray())) {
                $module->grantGroupAccess($group);
            }
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Groups have been added to this module successfully!'
        ], 200);
    }

    public function update(Request $request, $module)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'path' => 'required|string',
            'component' => 'required|string',
            'isAuthRequired' => 'required',
            'parentId' => 'required',
            'type' => 'required|string|in:application,module,page'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $module = Module::find($module);

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $module->update([
            'name' => $request->name,
            'label' => Str::slug($request->name),
            'path' => $request->path,
            'component' => $request->component,
            'icon' => $request->icon,
            'parentId' => $request->parentId,
            'isAuthRequired' => $request->isAuthRequired,
            'isMenu' => $request->isMenu,
            'isAdministration' => $request->isAdministration,
            'type' => $request->type
        ]);

        if ($request->has('departments')) {
            $currentDepartments = $module->departments->pluck('id')->toArray();

            foreach($request->departments as $department) {
                $dept = Department::find($department['value']);

                if ($dept && ! in_array($dept->id, $currentDepartments)) {
                    $module->departments()->save($dept);
                }
            }
        }

        if ($request->has('roles')) {
            $currentRoles = $module->roles->pluck('id')->toArray();

            foreach($request->roles as $role) {
                $r = Role::find($role['value']);

                if ($r && ! in_array($r->id, $currentRoles)) {
                    $module->roles()->save($r);
                }
            }
        }

        return response()->json([
            'data' => new ModuleResource($module),
            'status' => 'success',
            'message' => 'Module updated successfully!'
        ], 200);
    }

    public function destroy($module)
    {
        $module = Module::find($module);

        if (! $module) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $module;
        $module->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Module deleted successfully!'
        ], 200);
    }
}
