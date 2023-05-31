<?php

use Illuminate\Database\Seeder;
use Junges\ACL\Http\Models\Group;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            'Systemadministrator', 'vACC Leitung', 'ATD Leitung', 'PTD Leitung', 'RG Leitung', 'NAV Leitung', 'Event Leitung',
            'Tech Leitung', 'PMP Leitung', 'ATD Prüfer', 'PTD Prüfer', 'PTD Trainer', 'RG Mentor', 'RG NAV',
            'RG Event', 'VFR Moderator', 'Test Team',
        ];

        foreach ($groups as $group) {
            Group::create([
                'name' => $group,
                'slug' => Illuminate\Support\Str::slug($group, '.'),
                'description' => $group,
            ]);
        }
    }
}
