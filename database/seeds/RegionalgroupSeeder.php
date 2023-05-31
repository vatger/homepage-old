<?php

use Illuminate\Database\Seeder;

class RegionalgroupSeeder extends Seeder
{

	private $firs = [
		'FIR Bremen',
		'FIR Langen',
		'FIR Munich',
	];

	private $regionalgroups = [
        ['name' => 'RG Bremen', 'fir_id' => 1],
        ['name' => 'RG Berlin', 'fir_id' => 1],
		['name' => 'RG Düsseldorf', 'fir_id' => 2],
		['name' => 'RG Frankfurt', 'fir_id' => 2],
		['name' => 'RG München', 'fir_id' => 3],
	];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('regionalgroups_firs')->truncate();
        DB::table('regionalgroups_regionalgroups')->truncate();

        foreach ($this->firs as $fir) {
        	$f = new \App\Models\Regionalgroups\FlightInformationRegion();
        	$f->name = $fir;
        	$f->save();
        }

        foreach ($this->regionalgroups as $rg) {
        	$regionalgroup = new \App\Models\Regionalgroups\Regionalgroup();
        	$regionalgroup->name = $rg['name'];
        	$regionalgroup->fir_id = $rg['fir_id'];
        	$regionalgroup->save();
        }
    }
}
