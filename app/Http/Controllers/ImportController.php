<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Http\Resources\SubBudgetHeadResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ModuleResource;
use App\Models\BudgetHead;
use App\Models\Department;
use App\Models\Role;
use App\Models\Module;
use App\Models\SubBudgetHead;
use App\Models\CreditBudgetHead;
use App\Models\User;
use App\Models\GradeLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;

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
            case "modules" :
                $this->result = $this->moduleBulkAdd($request->data);
                break;
            case "roles" :
                $this->result = $this->rolesBulkAdd($request->data);
                break;
            case "grade-levels" :
                $this->result = $this->gradeLevelBulkAdd($request->data);
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
        $dataChunk = [];

        foreach ($data as $key => $value) {
            $budgetHead = BudgetHead::where('budgetId', $value['BN'])->first();

            if (! $budgetHead) {
                $insertData = [
                    'budgetId' => $value['BN'],
                    'name' => $value['NAME'],
                    'label' => Str::slug($value['NAME'])
                ];

                $dataChunk[] = $insertData;
            }
            

        }

        $dataChunk = collect($dataChunk);
        $chunks = $dataChunk->chunk(100);
        return $this->insertInto('budget_heads', $chunks);
    }

    protected function rolesBulkAdd(array $data)
    {
        $dataChunk = [];

        foreach ($data as $key => $value) {
            $label = Str::slug($value['NAME']);
            $role = Role::where('label', $label)->first();

            if (! $role) {
                $insertData = [
                    'name' => $value['NAME'],
                    'label' => $label,
                    'max_slots' => $value['SLOT'],
                    'isSuper' => $value['SUPER'] == 1 ? true : false, 
                    'cannot_expire' => $value['EXPIRE'] == 1 ? true : false,
                    'start_date' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $dataChunk[] = $insertData;
            }
            

        }

        $dataChunk = collect($dataChunk);
        $chunks = $dataChunk->chunk(100);
        return $this->insertInto('roles', $chunks);
    }

    protected function subBudgetHeadBulkAdd(array $data)
    {
        foreach ($data as $value) {
            $budgetHead = BudgetHead::where('budgetId', $value['BH'])->first();
            $department = Department::where('code', $value['DEPARTMENT'])->first();

            if ($budgetHead && $department) {
                $subBudgetHead = SubBudgetHead::create([
                    'budget_head_id' => $budgetHead->id,
                    'department_id' => $department->id,
                    'budgetCode' => $value['CODE'],
                    'name' => $value['NAME'],
                    'label' => Str::slug($value['NAME']),
                    'description' => "EMPTY VALUE",
                    'type' => "capital",
                    'active' => true
                ]);

                $fund = CreditBudgetHead::create([
                    'sub_budget_head_id' => $subBudgetHead->id,
                    'description' => 'FUNDING',
                    'approved_amount' => $value['APPROVED'],
                    'booked_balance' => $value['APPROVED'],
                    'actual_balance' => $value['APPROVED'],
                    'budget_year' => config('settings.budget_year') ?? config('budget.budget_year')
                ]);
            }

            $this->bulkRecords[] = $subBudgetHead;
        }

        return $this->bulkRecords;
    }

    protected function departmentBulkAdd(array $data)
    {
        foreach ($data as $value) {
            $department = Department::where('code', $value['Code'])->first();

            if (! $department) {

                $parent = $value['Parent'] !== 'NONE' ? Department::where('code', $value['Parent'])->first() : null;
                $parentId = $parent !== null ? $parent->id : 0;

                $department = Department::create([
                    'name' => $value['Name'],
                    'label' => Str::slug($value['Name']),
                    'code' => $value['Code'],
                    'type' => strtolower($value['Type']),
                    'parentId' => $parentId,
                ]);
            }

            $this->bulkRecords[] = $department;
        }

        return DepartmentResource::collection($this->bulkRecords);
    }

    protected function moduleBulkAdd(array $data)
    {
        foreach($data as $value) {
            $label = Str::slug($value['name']);
            $module = Module::where('label', $label)->first();

            if (! $module) {

                if ($value['parent'] !== "none") {
                    $this->parent = Module::where('label', $value['parent'])->first();
                }

                $module = Module::create([
                    'name' => $value['name'],
                    'label' => $label,
                    'path' => $value['path'],
                    'generatePermissions' => $value['generatePermissions'] == 1 ? true : false,
                    'isAdministration' => $value['isAdministration'] == 1 ? true : false,
                    'type' => $value['type'],
                    'parentId' => $this->parent ? $this->parent->id : 0
                ]);

                if ($value['generatePermissions'] == 1) {
                    foreach ($module->normalizer($module->name) as $value) {
                        $permission = $module->savePermission($value, $module->name);

                        if ($permission != null) {
                            $module->addPermission($permission);
                        }
                    }
                }

            }

            $this->bulkRecords[] = $module;
        }

        return ModuleResource::collection($this->bulkRecords);
    }

    protected function gradeLevelBulkAdd(array $data)
    {
        foreach($data as $value) {
            $label = Str::slug($value['name']);
            $gradeLevel = GradeLevel::where('label', $label)->first();

            if (! $gradeLevel) {

                $gradeLevel = GradeLevel::create([
                    'name' => $value['name'],
                    'label' => $label,
                    'code' => $value['key'],
                ]);

            }

            $this->bulkRecords[] = $gradeLevel;
        }

        return $this->bulkRecords;
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

    protected function insertInto($table, $chunks) 
    {
        foreach ($chunks as $chunk) {
            DB::table($table)->insert($chunk->toArray());
        }

        return;
    }
}
