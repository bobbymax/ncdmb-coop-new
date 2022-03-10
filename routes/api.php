<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::post('login', 'AuthApiController@login');
    Route::get('entitlements/dependencies', 'EntitlementController@getEntitlementDependencies');
    Route::post('load/entitlements', 'EntitlementController@saveBatchEntitlements');
    Route::get('load/wages/dependencies', 'PriceListController@getDependencies');
    Route::get('fetch/users/{user}', 'StaffController@fetStaffRecord');

    // Configuration Routes
    Route::get('get/models', 'ConfigurationController@fetchModels');
    Route::get('get/models/{model}/columns', 'ConfigurationController@getColumns');
    Route::get('application/modules', 'ModuleController@fetchApplications');

    // Imports
    Route::post('imports', 'ImportController@import');
    Route::get('budgetSummary', 'BudgetSummaryController@getBudgetOverview');
    Route::post('getPerformance', 'BudgetSummaryController@chartDisplay');
    Route::get('departments/{department}/budget/summary', 'BudgetSummaryController@getBudgetSummary');
    Route::post('portal/configuration', 'ConfigurationController@update');
    Route::get('dashboard/overview', 'DashboardController@init');

    // Access Control
    Route::apiResource('roles', 'RoleController');
    Route::apiResource('modules', 'ModuleController');
    Route::apiResource('departments', 'DepartmentController');
    Route::apiResource('groups', 'GroupController');
    Route::apiResource('users', 'StaffController');
    Route::apiResource('workFlows', 'WorkFlowController');
    Route::apiResource('procedures', 'ProcedureController');
    Route::apiResource('approvals', 'ApprovalController');
    Route::apiResource('settings', 'SettingController');
    Route::apiResource('logisticsRequests', 'LogisticsController');
    Route::post('logisticsRequests/{logisticsRequest}/complete', 'LogisticsController@fulfillLogisticsRequest');

    // Budget Control
    Route::apiResource('budgetHeads', 'BudgetHeadController');
    Route::post('budgetHeads/import','BudgetHeadController@importedBudgetHeads');
    Route::get('budget/dependencies', 'SubBudgetHeadController@getDependencies');
    Route::apiResource('subBudgetHeads', 'SubBudgetHeadController');
    Route::apiResource('creditBudgetHeads', 'CreditBudgetHeadController');
    Route::post('budget/clear', 'ClaimController@budgetClear');
    Route::apiResource('expenditures', 'ExpenditureController');
    Route::apiResource('batches', 'BatchController');
    Route::get('reverse/batches/{batch}', 'BatchController@fetchBatchForReversal');
    Route::apiResource('priceLists', 'PriceListController');
    Route::apiResource('refunds', 'RefundController');
    Route::patch('batch/expenditures/{expenditure}', 'ExpenditureController@batchExpenditureUpdate');
    Route::post('clear/payments', 'BatchController@clearPayments');
    Route::get('batches/clear/query/{batch}', 'BatchController@clearBatchQuery');

    // Staff Structure
    Route::apiResource('gradeLevels', 'GradeLevelController');
    Route::apiResource('benefits', 'BenefitController');
    Route::apiResource('entitlements', 'EntitlementController');
    Route::apiResource('claims', 'ClaimController');
    Route::apiResource('claims/{claim}/instructions', 'InstructionController');
    Route::get('fetch/claims/{claim}', 'ClaimController@fetchClaimByCode');
    Route::post('claim/instructions', 'InstructionController@addClaimInstructions');
    Route::apiResource('touringAdvances', 'TouringAdvanceController');
    Route::patch('raise/touringAdvances/{touringAdvance}', 'TouringAdvanceController@changeTouringAdvanceStatus');

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
