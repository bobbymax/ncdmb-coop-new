<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Mail\LoginMail;
use Mail;

class AuthApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_no' => 'required|max:255',
            'password' => 'required|string'
        ]);

        // check if validation rules failed
        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'An error occured',
                'requess' => $request->all()
            ], 422);
        }

        $loginCredentials = $request->only('staff_no', 'password');

        if (! Auth::attempt($loginCredentials)) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid login details'
            ], 422);
        }

        $token = Auth::user()->createToken('authToken')->accessToken;

        if (Auth::user() && Auth::user()->email !== "admin@admin.com") {
            Mail::to(Auth::user()->email)->send(new LoginMail(Auth::user()));
        }

        return response()->json([
            'message' => 'Login Successful',
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => new UserResource(Auth::user()),
            ]
        ]);
    }

    public function addRoleToUser(Request $request, $user)
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

        $user = User::find($user);

        if (! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        foreach ($request->roles as $role) {
            $role = Role::find($role["value"]);

            if ($role && ! in_array($role->id, $user->roles->pluck('id')->toArray())) {
                $user->assignRole($role);
            }
        }

        return response()->json([
            'data' => new UserResource($user),
            'status' => 'success',
            'message' => 'Module updated successfully!'
        ], 200);
    }

    public function addDepartmentsToStaff(Request $request, $user)
    {
        $validator = Validator::make($request->all(), [
            'departments' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $user = User::find($user);

        if (! $user) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        foreach($request->departments as $value) {
            $department = Department::find($value);

            if ($department && ! in_array($department->id, $user->departments->pluck('id')->toArray())) {
                $user->addDepartment($department);
            }
        }

        return response()->json([
            'data' => new UserResource($user),
            'status' => 'success',
            'message' => 'Departments added successfully!'
        ], 200);
    }
}
