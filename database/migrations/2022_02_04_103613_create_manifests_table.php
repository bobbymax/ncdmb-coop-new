<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManifestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manifests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('module_id')->unsigned();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');

            $table->string('name');
            $table->string('input_type');
            $table->integer('grid');
            $table->string('defaultValue')->nullable();
            $table->string('placeholder')->nullable();
            $table->json('details')->nullable();
            $table->boolean('browse')->default(false);
            $table->boolean('read')->default(false);
            $table->boolean('edit')->default(false);
            $table->boolean('add')->default(false);
            $table->boolean('delete')->default(false);
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
        Schema::dropIfExists('manifests');
    }
}
