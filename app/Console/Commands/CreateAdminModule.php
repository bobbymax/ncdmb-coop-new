<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Support\Str;

class CreateAdminModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:module {name} {path} {type} {component} {parent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Admin Modules';

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
        return $this->createAdminModules();
    }

    protected function createAdminModules()
    {

        $this->info('In progress...');
        $parent = $this->argument('parent') !== 'none' ? Module::where('label', $this->argument('parent'))->first() : null;

        $this->info('Creating module....');
        $module = Module::create([
            'name' => $this->argument('name'),
            'label' => Str::slug($this->argument('name')),
            'icon' => 'fa-settings',
            'path' => $this->argument('path'),
            'component' => $this->argument('component'),
            'type' => $this->argument('type'),
            'parentId' => $parent !== null ? $parent->id : 0,
            'generatePermissions' => true,
            'isAdministration' => true
        ]);
        $this->info('Module created successfully...');
        $this->info('Generating module permissions');
        $this->generatePermissions($module);
        $this->info('Permissions generated successfully...');
    }

    protected function generatePermissions($module)
    {
        foreach ($module->normalizer($module->name) as $value) {
            $permission = $module->savePermission($value, $module->name);

            if ($permission != null) {
                $module->addPermission($permission);
            }
        }

        return $module;
    }
}
