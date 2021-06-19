<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('benefit_id')->unsigned();
            $table->foreign('benefit_id')->references('id')->on('benefits')->onDelete('cascade');
            $table->string('additional_benefit')->nullable();
            $table->date('from');
            $table->date('to')->nullable();
            $table->text('description');
            $table->decimal('amount', $precision = 30, $scale = 2)->default(0);
            $table->bigInteger('instructionable_id')->unsigned();
            $table->string('instructionable_type');
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
        Schema::dropIfExists('instructions');
    }
}
