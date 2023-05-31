<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionalgroupsTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regionalgroups_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regionalgroup_id');
            $table->string('name', 25);
            $table->integer('order')->unsigned()->default(0);
            $table->longText('message');
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
        Schema::dropIfExists('regionalgroups_templates');
    }
}
