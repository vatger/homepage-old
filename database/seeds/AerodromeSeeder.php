<?php

use Illuminate\Database\Seeder;

class AerodromeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $airports = json_decode(\Illuminate\Support\Facades\Storage::get('navigation/aerodromes.json'), true);
        
        $ac = count($airports);
        $this->command->getOutput()->writeln('Loaded '.$ac.' aerodromes from file.');
        
        DB::table('navigation_aerodromes')->truncate();
        $this->command->getOutput()->writeln('Truncated aerodromes table.');

        $this->command->getOutput()->writeln('Starting seeding of new information...');
        $this->command->getOutput()->progressStart($ac);

        foreach ($airports as $icao => $data) {
            $a = new \App\Models\Navigation\Aerodrome();
            $a->icao = $data['icao'];
            $a->name = $data['name'];
            $a->description = '';
            $a->iata = $data['iata'];
            $a->elevation = (float) $data['elevation'];
            $a->latitude = (float) $data['lat'];
            $a->longitude = (float) $data['lon'];
            $a->city = $data['city'];
            $a->country = $data['country'];
            $a->state = $data['state'];
            $a->save();
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->getOutput()->writeln('Finished seeding.');
    }
}
