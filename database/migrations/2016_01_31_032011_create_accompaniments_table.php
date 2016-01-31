<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccompanimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accompaniments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id_nutritionist')->unsigned();
            $table->integer('user_id_client')->unsigned();
            $table->date('begin_date');
            $table->date('end_date');
            $table->timestamps();

            $table->index('user_id_nutritionist');
            $table->index('user_id_client');

            $table->foreign('user_id_nutritionist', 'idx_accompaniments_users_nutritionist')
                ->references('id')
                ->on('users');

            $table->foreign('user_id_client', 'idx_accompaniments_users_client')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accompaniments', function(Blueprint $table) {
            $table->dropForeign('idx_accompaniments_users_nutritionist');
            $table->dropForeign('idx_accompaniments_users_client');
        });

        Schema::drop('accompaniments');
    }
}
