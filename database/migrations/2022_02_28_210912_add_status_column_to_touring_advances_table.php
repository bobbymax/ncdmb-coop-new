<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToTouringAdvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('touring_advances', function (Blueprint $table) {
            $table->enum('claim_status', ['draft', 'submitted'])->default('draft')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('touring_advances', function (Blueprint $table) {
            $table->dropColumn('claim_status');
        });
    }
}
