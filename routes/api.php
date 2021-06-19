<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::post('login', 'AuthApiController@login');

    // Configuration Routes
    Route::get('get/models', 'ConfigurationController@fetchModels');
    Route::get('get/models/{model}/columns', 'ConfigurationController@getColumns');

    // Access Control
    Route::apiResource('roles', 'RoleController');
    Route::apiResource('modules', 'ModuleController');
    Route::apiResource('departments', 'DepartmentController');
    Route::apiResource('groups', 'GroupController');
    Route::apiResource('users', 'StaffController');

    // Budget Control
    Route::apiResource('budgetHeads', 'BudgetHeadController');
    Route::apiResource('subBudgetHeads', 'SubBudgetHeadController');
    Route::apiResource('creditBudgetHeads', 'CreditBudgetHeadController');
    Route::apiResource('expenditures', 'ExpenditureController');
    Route::apiResource('batches', 'BatchController');

    // Staff Structure
    Route::apiResource('gradeLevels', 'GradeLevelController');
    Route::apiResource('benefits', 'BenefitController');
    Route::apiResource('entitlements', 'EntitlementController');
    Route::apiResource('claims', 'ClaimController');
    Route::apiResource('claims/{claim}/instructions', 'InstructionController');

    // Additional Access Control Routes
    Route::post('groups/{group}/staffs', 'GroupController@addStaffsToGroup');
    Route::post('modules/{module}/groups', 'ModuleController@grantGroupsAccess');
    Route::post('modules/{module}/roles', 'ModuleController@addRolesToModule');
    Route::post('users/{user}/roles', 'AuthApiController@addRoleToUser');
    Route::post('users/{user}/departments', 'AuthApiController@addDepartmentsToStaff');
    Route::post('departments/{department}/modules', 'DepartmentController@addModulesToDepartment');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@ncdmb.gov.ng'
    ], 404);
});
