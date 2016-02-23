<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMfLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      if (Schema::hasTable('mf_logs')) return;

       Schema::create('mf_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('loggable_id');
            $table->string('loggable_type');
            $table->string('action');
            $table->text('before')->nullable();
            $table->text('after')->nullable();
            $table->datetime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mf_logs');
    }
}
