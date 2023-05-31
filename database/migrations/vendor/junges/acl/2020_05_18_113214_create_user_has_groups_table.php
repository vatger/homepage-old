<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $userHasGroupsTable = config('acl.tables.user_has_groups', 'user_has_groups');
        $usersTable = config('acl.tables.users', 'users');
        $groupsTable = config('acl.tables.groups', 'groups');
        Schema::create($userHasGroupsTable,
            function (Blueprint $table) use ($usersTable, $groupsTable) {
                $table->unsignedInteger('account_id', false, true);
                $table->bigInteger('group_id', false, true);
                $table->foreign('account_id', 'ahg_acc_id')
                    ->references('id')
                    ->on($usersTable)
                    ->onDelete('cascade');
                $table->foreign('group_id', 'ahg_grp_id')
                    ->references('id')
                    ->on($groupsTable)
                    ->onDelete('cascade');
                $table->primary(['account_id', 'group_id'], 'ahg_primary');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $userHasGroupsTable = config('acl.tables.user_has_groups', 'user_has_groups');
        Schema::dropIfExists($userHasGroupsTable);
    }
}
