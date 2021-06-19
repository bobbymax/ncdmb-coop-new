<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class ConfigurationController extends Controller
{
	public $path, $columns;

	protected $models = [];

    // public function __construct()
    // {
    // 	$this->middleware('auth:api');
    // }

    public function fetchModels()
    {
    	return response()->json([
    		'data' => $this->getModels(),
    		'status' => 'success',
    		'message' => 'Models from API Endpoint'
    	], 200);
    }

    public function getColumns($model)
    {
    	// dd(); Str::slug(Str::snake($model_class))
    	$model = ucfirst(Str::camel($model));

    	if (! in_array(ucwords($model), $this->getModels())) {
    		return response()->json([
    			'data' => null,
    			'status' => 'error',
    			'message' => 'There are no columns because this model name does not exist'
    		], 404);
    	}

    	$tablename = Str::plural(Str::snake($model));
    	$this->columns = Schema::getColumnListing($tablename);

    	return response()->json([
    		'data' => [
    			'columns' => $this->columns,
    			'table' => $tablename
    		],
    		'status' => 'success',
    		'message' => 'Table columns list'
    	], 200);

    }

    private function getModels()
    {
    	$this->path = app_path() . "/Models";

    	foreach (scandir($this->path) as $model) {
    		if ($model === '.' || $model === '..') continue;
    		$this->models[] = substr($model, 0, -4);
    	}

    	return $this->models;
    }
}
