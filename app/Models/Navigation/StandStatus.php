<?php

namespace App\Models\Navigation;

use CobaltGrid\VatsimStandStatus\StandStatus as BaseStatus;
use Illuminate\Support\Str;

/**
 * Stand Status
 *
 * This class offers the oportunities required to check if stands are available at an aerodrome
 */
class StandStatus extends BaseStatus
{

    private $maxStandDistance = 0.07; // In kilometeres
    private $hideStandSidesWhenOccupied = true;
    private $maxDistanceFromAirport = 2; // In kilometeres
    private $maxAircraftAltitude = 3000; // In feet
    private $maxAircraftGroundspeed = 10; // In knots
	
	/**
	 * Initialize the Status class
	 * @param [type]  $icao               [description]
	 * @param [type]  $standFile          [description]
	 * @param [type]  $latitude           [description]
	 * @param [type]  $longitude          [description]
	 * @param boolean $parseData          [description]
	 * @param [type]  $maxAirportDistance [description]
	 */
	function __construct($icao, $standFile, $latitude, $longitude, $parseData = true, $maxAirportDistance = null)
	{
		parent::__construct($icao, $standFile, $latitude, $longitude, $parseData, $maxAirportDistance);
	}

	/**
	 * Load stand definitions from standFile
	 * 
	 * @return [type] [description]
	 */
	public function loadStandsData()
	{
		$array = $fields = [];
        $i = 0;
        $handle = @fopen($this->airportStandsFile, 'r');
        if ($handle) {
            while (false !== ($row = fgetcsv($handle, 4096))) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                $y = 0;
                foreach ($row as $k => $value) {
                    if (1 == $y) { // Convert LAT coordinate
                        // $array[$row[0]][$fields[$k]] = $this->convertCAALatCoord($value);
                        $array[$row[0]][$fields[$k]] = $value;
                    } elseif (2 == $y) { // Convert LONG coordinate
                        // $array[$row[0]][$fields[$k]] = $this->convertCAALongCoord($value);
                        $array[$row[0]][$fields[$k]] = $value;
                    } else {
                        $array[$row[0]][$fields[$k]] = $value;
                    }
                    ++$y;
                }
                ++$i;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";

                return false;
            }
            fclose($handle);
        } else {
            return false;
        }
        $this->stands = $array;

        return true;
	}

	/**
	 * Does a stand has sidestands?
	 * 
	 * @param  [type] $standId [description]
	 * @return [type]          [description]
	 */
	public function standSides($standID)
	{
		$standSides = [];
        $stands = $this->stands;

        // Consider only those sidestands that are stands with appendix
        // R, L, A, B, C
        // but start in the same way as a normal stand
        // so we will check if a stand ends on one of those letter and remove it
        $standBase = '';
        if (Str::endsWith($standID, 'R')) {
            $standBase = Str::replaceLast('R', '', $standID);
        }
        if (Str::endsWith($standID, 'L')) {
            $standBase = Str::replaceLast('L', '', $standID);
        }
        if (Str::endsWith($standID, 'A')) {
            $standBase = Str::replaceLast('A', '', $standID);
        }
        if (Str::endsWith($standID, 'B')) {
            $standBase = Str::replaceLast('B', '', $standID);
        }
        if (Str::endsWith($standID, 'C')) {
            $standBase = Str::replaceLast('C', '', $standID);
        }

        // Check if stand has a side already
        if (Str::endsWith($standID, 'R') || Str::endsWith($standID, 'L')) {
            // Our stand is already L/R
            if (Str::endsWith($standID, 'R')) {
                if (isset($stands[$standBase.'L'])) {
                    $standSides[] = $standBase.'L';
                }
                if (isset($stands[$standBase.'C'])) {
                    $standSides[] = $standBase.'C';
                }
                if (isset($stands[$standBase])) {
                    $standSides[] = $standBase;
                }
            }
            if (Str::endsWith($standID, 'L')) {
                if (isset($stands[$standBase.'R'])) {
                    $standSides[] = $standBase.'R';
                }
                if (isset($stands[$standBase.'C'])) {
                    $standSides[] = $standBase.'C';
                }
                if (isset($stands[$standBase])) {
                    $standSides[] = $standBase;
                }
            }
        } elseif (strstr($standID, 'A') || strstr($standID, 'B')) {
            // Our stand already is A / B
            if (Str::endsWith($standID, 'A')) {
                if (isset($stands[$standBase.'B'])) {
                    $standSides[] = $standBase.'B';
                }
                if (isset($stands[$standBase.'C'])) {
                    $standSides[] = $standBase.'C';
                }
                if (isset($stands[$standBase])) {
                    $standSides[] = $standBase;
                }
            }
            if (Str::endsWith($standID, 'B')) {
                if (isset($stands[$standBase.'A'])) {
                    $standSides[] = $standBase.'A';
                }
                if (isset($stands[$standBase.'C'])) {
                    $standSides[] = $standBase.'C';
                }
                if (isset($stands[$standBase])) {
                    $standSides[] = $standBase;
                }
            }
        } else {
            // Stand itself has no side, but may have L / R / A / B sides
            if (isset($stands[$standBase.'L'])) {
                $standSides[] = $standBase.'L';
            }
            if (isset($stands[$standBase.'R'])) {
                $standSides[] = $standBase.'R';
            }
            if (isset($stands[$standBase.'C'])) {
                $standSides[] = $standBase.'C';
            }
            if (isset($stands[$standBase.'A'])) {
                $standSides[] = $standBase.'A';
            }
            if (isset($stands[$standBase.'B'])) {
                $standSides[] = $standBase.'B';
            }
        }

        if (0 == count($standSides)) {
            return false;
        } else {
            return $standSides;
        }
	}

    public function getAircraftWithinParameters()
    {
        
        $pilots = \App\Models\Network\PilotClient::online()->withinAirport($this->airportICAO)->get();

        $filteredResults = [];
        foreach ($pilots as $pilot) {
            $d = $this->getCoordDistance($pilot->current_latitude, $pilot->current_longitude, $this->airportCoordinates['lat'], $this->airportCoordinates['long']);
            // \Log::info('Flight: '.$pilot->callsign.' has distance '.$d.' to icao '.$this->airportICAO);
            if($d <= $this->getMaxDistanceFromAirport()) {
                //$pilots[] = array('callsign' => "TEST", "latitude" => 55.949228, "longitude" => -3.364303, "altitude" => 0, "groundspeed" => 0, "planned_destairport" => "TEST", "planned_depairport" => "TEST");
                $filteredResults[] = array(
                    'callsign' => $pilot->callsign,
                    'latitude' => $pilot->current_latitude,
                    'longitude' => $pilot->current_longitude,
                    'altitude' => $pilot->current_altitude,
                    'groundspeed' => $pilot->current_groundspeed,
                    'planned_destairport' => $pilot->arrival_airport,
                    'planned_depairport' => $pilot->departure_airport,
                );
            }
        }
        $this->aircraftSearchResults = $filteredResults;
        return true;
    }
}