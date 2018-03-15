<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {

            $users_table= config('polls.users_table','users');
            $users_primary_key = config('polls.users_primary_key','id');

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('option_id')->unsigned();
            $table->smallInteger('weight')->default(1);

            $table->unique(['user_id','option_id']);
            $table->foreign('option_id')->references('id')->on('options');
            $table->foreign('user_id')->references($users_primary_key)->on($users_table);
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
        Schema::drop('votes');
    }
}
