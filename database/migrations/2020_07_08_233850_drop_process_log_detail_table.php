<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropProcessLogDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('process_log_detail');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('process_log_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('process_log_id');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
