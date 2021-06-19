<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entitlements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('grade_level_id')->unsigned();
            $table->foreign('grade_level_id')->references('id')->on('grade_levels')->onDelete('cascade');
            $table->bigInteger('benefit_id')->unsigned();
            $table->foreign('benefit_id')->references('id')->on('benefits')->onDelete('cascade');
            $table->decimal('amount', $precision = 30, $scale = 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entitlements');
    }
}
