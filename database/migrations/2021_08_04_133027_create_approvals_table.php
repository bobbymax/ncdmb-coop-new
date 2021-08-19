<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('work_flow')->nullable();
            $table->string('level')->nullable();
            $table->bigInteger('approveable_id')->unsigned();
            $table->string('approveable_type');
            $table->longText('description')->nullable();
            $table->longText('response')->nullable();
            $table->enum('status', ['pending', 'in-progress', 'approved', 'denied', 'queried'])->default('pending');
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
        Schema::dropIfExists('approvals');
    }
}
