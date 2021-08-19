<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('batch_no')->unique();
            $table->decimal('amount', $precision = 30, $scale = 2)->default(0);
            $table->integer('noOfClaim')->default(0);
            $table->enum('level', ['budget-office', 'treasury', 'audit'])->default('budget-office');
            $table->enum('status', ['pending', 'registered', 'queried', 'paid', 'archived'])->default('pending');
            $table->boolean('budget')->default(false);
            $table->boolean('treasury')->default(false);
            $table->boolean('audit')->default(false);
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
        Schema::dropIfExists('batches');
    }
}
