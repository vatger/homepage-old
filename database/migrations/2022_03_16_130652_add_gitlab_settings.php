<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGitlabSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_account_settings', function (Blueprint $table) {
            $table->unsignedInteger('gitlab_id')->nullable()->after('forum_id');
        });
        \Illuminate\Support\Facades\DB::table('membership_permissions')
            ->insert(array('name'=>'Gitlab Account Verwaltung', 'slug'=>'administration.services.gitlab', 'description'=>'Erlaubt das Anlegen und Verwalten des eigenen Gitlabaccounts.',
                'created_at'=>\Carbon\Carbon::now(),
                'updated_at'=>\Carbon\Carbon::now
                ()));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_account_settings', function (Blueprint $table) {
            $table->dropColumn('gitlab_id');
        });
    }
}
