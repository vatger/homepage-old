<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingTablesAtd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('uts_atd_trainings', function (Blueprint $table)
        {
            $table->id();
            $table->unsignedInteger('regionalgroup_id');
            $table->unsignedInteger('trainee_id');
            $table->timestamps();
        });

        Schema::create('uts_atd_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('training_id');
            $table->unsignedInteger('mentor_id')->nullable();
            $table->unsignedInteger('second_mentor_id')->nullable();
            $table->unsignedInteger('station_id');
            $table->unsignedInteger('type');
            $table->longtext('mentor_comment_internal')->nullable();
            $table->longtext('mentor_comment_external')->nullable();
            $table->boolean('recomendation_solo')->default(false);
            $table->boolean('recomendation_cpt')->default(false);
            $table->enum('traffic', ['low', 'medium', 'high'])->nullable();
            $table->enum('complexity', ['easy', 'normal', 'difficult'])->nullable();
            $table->unsignedInteger('phraso_vfr_de')->default(0);
            $table->unsignedInteger('phraso_vfr_en')->default(0);
            $table->unsignedInteger('phraso_ifr_en')->default(0);

            $table->unsignedInteger('clearances')->default(0);
            $table->unsignedInteger('conditionals')->default(0);
            $table->unsignedInteger('handling_vfr')->default(0);
            $table->unsignedInteger('handling_ifr')->default(0);
            $table->unsignedInteger('ifr_pickup_cancelation')->default(0);
            $table->unsignedInteger('vectoring')->default(0);
            $table->unsignedInteger('separation')->default(0);
            $table->unsignedInteger('speedmanagement')->default(0);

            $table->unsignedInteger('departurelist')->default(0);
            $table->unsignedInteger('tags')->default(0);

            $table->unsignedInteger('handoff')->default(0);
            $table->unsignedInteger('priorities')->default(0);
            $table->unsignedInteger('bigpicture')->default(0);

            $table->boolean('accepted')->default(false);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uts_atd_sessions');
        Schema::dropIfExists('uts_atd_trainings');
    }
}
