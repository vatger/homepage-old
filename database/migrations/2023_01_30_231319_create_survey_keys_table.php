<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyKeysTable extends Migration
{
    public function up()
    {
        Schema::create('survey_keys', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->unsigned();
            $table->string('name');
            $table->string('token');
            $table->string('url');
            $table->timestamp('valid_till')->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('membership_accounts');
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_keys');
    }
}
