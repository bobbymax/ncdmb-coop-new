<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Models\Module;
use App\Models\GradeLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user for the budget portal';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return $this->assignAdminRoleToUser();
    }

    protected function createRole()
    {
        $role = Role::create([
            'name' => 'Super Administrator',
            'label' => 'super-administrator',
            'max_slots' => 1,
            'start_date' => Carbon::now(),
            'expiry_date' => null,
            'isSuper' => true,
            'cannot_expire' => true
        ]);

        return $role;
    }

    protected function createDepartment()
    {
        $department = Department::create([
            'name' => 'Administration Department',
            'label' => 'administration-department',
            'code' => 'SAD',
            'type' => 'directorate',
            'parentId' => 0
        ]);

        return $department;
    }

    protected function createGradeLevel()
    {
        $gradeLevel = GradeLevel::create([
            'name' => 'Super Admin',
            'label' => 'super-admin',
            'code' => 'SA1'
        ]);

        return $gradeLevel;
    }

    protected function createUser()
    {
        $this->info('Creating admin department...');
        $department = $this->createDepartment();
        $this->info('Done!!');
        $this->info('Creating admin grade level...');
        $grade = $this->createGradeLevel();
        $this->info('Admin Grade Level Added!!');

        $this->info('Creating admin user record...');
        $staff = User::create([
            'staff_no' => 'SUPER',
            'grade_level_id' => $grade->id,
            'name' => 'ICT Super Admin',
            'email' => $this->argument('email'),
            'password' => Hash::make('password'),
            'department_id' => $department->id,
            'isAdministrator' => true
        ]);
        $this->info('Admin User record created successfully...');
        return $staff;
    }

    protected function assignAdminRoleToUser()
    {
        $this->info('Process starting...');
        $user = $this->createUser();
        $this->info('Creating admin user role...');
        $role = $this->createRole();
        $this->info('Adding admin user to role..');
        $user->roles()->save($role);
        $this->info('User assigned admin role successfully!!');
    }
}
