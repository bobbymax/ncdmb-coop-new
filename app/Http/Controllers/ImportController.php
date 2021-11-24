<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Http\Resources\SubBudgetHeadResource;
use App\Http\Resources\UserResource;
use App\Models\BudgetHead;
use App\Models\Department;
use App\Models\Role;
use App\Models\SubBudgetHead;
use App\Models\CreditBudgetHead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    protected $bulkRecords = [];
    protected $result, $parent;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required',
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'status' => 'danger',
                'message' => 'Please fix errors'
            ], 500);
        }

        switch ($request->type) {
            case "departments" :
                $this->result = $this->departmentBulkAdd($request->data);
                break;
            case "staff" :
                $this->result = $this->staffBulkAdd($request->data);
                break;
            case "budget-heads" :
                $this->result = $this->budgetHeadBulkAdd($request->data);
                break;
            case "sub-budget-heads" :
                $this->result = $this->subBudgetHeadBulkAdd($request->data);
                break;
            default :
                $this->result = [];
                break;
        }

        return response()->json([
            'data' => $this->result,
            'status' => 'success',
            'message' => 'Imported successfully!!'
        ], 200);
    }

    protected function budgetHeadBulkAdd(array $data)
    {
        foreach ($data as $value) {
            $budgetHead = BudgetHead::where('budgetId', $value['S/NO'])->first();

            if (! $budgetHead) {
                $budgetHead = BudgetHead::create([
                    'budgetId' => $value['S/NO'],
                    'name' => $value['BUDGET HEAD'],
                    'label' => Str::slug($value['BUDGET HEAD'])
                ]);
            }

            $this->bulkRecords[] = $budgetHead;
        }

        return $this->bulkRecords;
    }

    protected function subBudgetHeadBulkAdd(array $data)
    {
        foreach ($data as $value) {
            $subBudgetHead = SubBudgetHead::where('budgetCode', $value['BUDGET CODES'])->first();

            if (! $subBudgetHead) {
                $budgetHead = BudgetHead::where('budgetId', $value['BUDGET HEAD'])->first();
                $department = Department::where('code', $value['DEPARTMENT'])->first();

                if ($budgetHead && $department) {
                    $subBudgetHead = SubBudgetHead::create([
                        'budget_head_id' => $budgetHead->id,
                        'department_id' => $department->id,
                        'budgetCode' => $value['BUDGET CODES'],
                        'name' => $value['SUB-BUDGET HEAD'],
                        'description' => "EMPTY VALUE",
                        'type' => "capital",
                        'active' => true
                    ]);

                    $fund = CreditBudgetHead::create([
                        'sub_budget_head_id' => $subBudgetHead->id,
                        'description' => 'EMPTY VALUE',
                        'approved_amount' => $value['APPROVED AMOUNT'],
                        'booked_balance' => $value['APPROVED AMOUNT'],
                        'actual_balance' => $value['APPROVED AMOUNT'],
                        'budget_year' => date('Y')
                    ]);
                }
            }

            $this->bulkRecords[] = $subBudgetHead;
        }

        return SubBudgetHeadResource::collection($this->bulkRecords);
    }

    protected function departmentBulkAdd(array $data)
    {
        foreach ($data as $value) {
            $department = Department::where('code', $value['Code'])->first();

            if (! $department) {

                if ($value['Parent'] !== "NONE") {
                    $this->parent = Department::where('code', $value['Parent'])->first();
                }

                $department = Department::create([
                    'name' => $value['Name'],
                    'label' => Str::slug($value['Name']),
                    'code' => $value['Code'],
                    'type' => strtolower($value['Type']),
                    'parentId' => $this->parent ? $this->parent->id : 0
                ]);
            }

            $this->bulkRecords[] = $department;
        }

        return DepartmentResource::collection($this->bulkRecords);
    }

    protected function staffBulkAdd(array $data)
    {
        foreach($data as $value) {
            $staff = User::where('email', $value['email'])->first();

            if (! $staff) {
                $staff = User::create([
                   'staff_no' => $value['staff_no'],
                   'email' => $value['email'],
                   'name' => $value['name'],
                   'password' => Hash::make('Password1'),
                ]);

                $role = Role::where('label', 'staff')->first();

                if (! $role) {
                    $role = Role::create([
                        'name' => 'Staff',
                        'label' => 'staff',
                        'max_slots' => 1000,
                        'start_date' => Carbon::now(),
                        'cannot_expire' => 1,
                    ]);
                }

                $staff->assignRole($role);
            }

            $this->bulkRecords[] = $staff;
        }

        return UserResource::collection($this->bulkRecords);
    }
}
