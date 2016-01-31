<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('accompaniment_id')->unsigned();
            $table->string('title');
            $table->dateTime('time');
            $table->dateTime('begin_date');
            $table->dateTime('end_date');
            $table->boolean('check')->default(false);
            $table->dateTime('checked_at')->nullable();
            $table->timestamps();

            $table->index('accompaniment_id');

            $table->foreign('accompaniment_id', 'idx_meals_accompaniments')
                ->references('id')
                ->on('accompaniments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meals', function(Blueprint $table) {
            $table->dropForeign('idx_meals_accompaniments');
        });

        Schema::drop('meals');
    }
}
