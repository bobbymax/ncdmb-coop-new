<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpendituresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenditures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('sub_budget_head_id')->unsigned();
            $table->foreign('sub_budget_head_id')->references('id')->on('sub_budget_heads')->onDelete('cascade');
            $table->bigInteger('claim_id')->unsigned();
            $table->foreign('claim_id')->references('id')->on('sub_budget_heads')->onDelete('cascade');
            $table->bigInteger('batch_id')->default(0);
            $table->enum('type', ['staff-claim', 'third-party', 'other'])->default('staff-claim');
            $table->enum('status', ['cleared', 'batched', 'queried', 'paid'])->default('cleared');
            $table->text('additional_info')->nullable();
            $table->boolean('closed')->default(false);
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
        Schema::dropIfExists('expenditures');
    }
}
