<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvenMoreColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('staff_no')->unique()->nullable()->after('id');
            $table->bigInteger('grade_level_id')->default(0)->after('staff_no');
            $table->bigInteger('department_id')->default(0)->after('grade_level_id');
            $table->decimal('points', $precision = 30, $scale = 2)->default(0)->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('staff_no');
            $table->dropColumn('grade_level_id');
            $table->dropColumn('department_id');
            $table->dropColumn('points');
        });
    }
}
