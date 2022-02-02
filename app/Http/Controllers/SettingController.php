<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Resources\SettingResource;

class SettingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::latest()->get();

        if ($settings->count() < 1) {
            return response()->json([
                'data' => [],
                'status' => 'info',
                'message' => 'No data found'
            ], 404);
        }

        return response()->json([
            'data' => SettingResource::collection($settings),
            'status' => 'success',
            'message' => 'Setting List'
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:settings',
            'input_type' => 'required|string',
            'group' => 'required|string|in:site,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $setting = Setting::create([
            'display_name' => $request->display_name,
            'key' => $request->key,
            'input_type' => $request->input_type,
            'group' => $request->group,
            'order' => isset($request->order) ? $request->order : 0,
            'details' => isset($request->details) ? $request->details : null
        ]);

        return response()->json([
            'data' => new SettingResource($setting),
            'status' => 'success',
            'message' => 'WorkFlow Created Successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show($setting)
    {
        $setting = Setting::find($setting);

        if (! $setting) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new SettingResource($setting),
            'status' => 'success',
            'message' => 'Setting Details'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit($setting)
    {
        $setting = Setting::find($setting);

        if (! $setting) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        return response()->json([
            'data' => new SettingResource($setting),
            'status' => 'success',
            'message' => 'Setting Details'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $setting)
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'input_type' => 'required|string',
            'group' => 'required|string|in:site,admin',
            'key' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'error',
                'message' => 'Please fix the following error(s):'
            ], 500);
        }

        $setting = Setting::find($setting);

        if (! $setting) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $setting->update([
            'display_name' => $request->display_name,
            'key' => $request->key,
            'input_type' => $request->input_type,
            'group' => $request->group,
            'order' => isset($request->order) ? $request->order : 0,
            'value' => isset($request->value) ? $request->value : null,
            'details' => isset($request->details) ? $request->details : null
        ]);

        return response()->json([
            'data' => new SettingResource($setting),
            'status' => 'success',
            'message' => 'Setting detail updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy($setting)
    {
        $setting = Setting::find($setting);

        if (! $setting) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        $old = $setting;
        $setting->delete();

        return response()->json([
            'data' => $old,
            'status' => 'success',
            'message' => 'Setting detail deleted successfully'
        ], 200);
    }
}
