<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Organization Details
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

	'organization' => [
		'name' => env('ORG_NAME', 'NCDMB'),
		'url' => env('ORG_URL', 'https://budget.ncdmb.gov.ng'),
		'endpoints' => env('ORG_API', 'https://budget-api.ncdmb.gov.ng')
	],


];