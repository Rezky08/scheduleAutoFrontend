<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCeleryIdToProcessLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('process_log', function (Blueprint $table) {
            $table->string('celery_id', 100)->after('item_key');
            $table->string('status', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('process_log', function (Blueprint $table) {
            $table->dropColumn('celery_id');
        });
    }
}
