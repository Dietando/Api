<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->char('token', 32);
            $table->timestamps();

            $table->index('user_id');

            $table->foreign('user_id', 'fk_auth_tokens_users')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auth_tokens', function(Blueprint $table) {
            $table->dropForeign('fk_auth_tokens_users');
        });

        Schema::drop('auth_tokens');
    }
}
