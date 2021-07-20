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
            $table->bigInteger('price_list_id')->unsigned();
            $table->foreign('price_list_id')->references('id')->on('price_lists')->onDelete('cascade');
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
