<?php

namespace App\Console\Commands\Navigation;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateFIRBoundaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nav:boundaries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate FIR Boundaries';

    /**
     * Path of the FIR Boundaries definition file.
     *
     * @var string
     */
    private $defFilePath = 'navigation/sectors/FIRBoundaries.dat';

    /**
     * Euroscope ESE File to generate local sectors from.
     *
     * @var string
     */
    private $eseFilePath = 'navigation/sectors/vacc.ese';

    /**
     * Euroscope Sector Definition file.
     *
     * @var string
     */
    private $sctFilePath = 'navigation/sectors/sector.sct';

    /**
     * Output path for the converted sectors.
     *
     * @var string
     */
    private $sectorOutputFile = 'navigation/sectors/fir_boundaries.json';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $output = [];
        if (Storage::exists($this->defFilePath)) {
            $defFile = Storage::get($this->defFilePath);
            $this->info('Definition file read');
            $sectors = [];
            $currentSector = [];
            foreach (explode("\n", $defFile) as $key => $line) {
                $line = str_replace("\r", '', $line);
                $lineSplit = explode('|', $line);
                if (10 == sizeof($lineSplit)) {
                    if ($key > 0) {
                        $sectors[] = $currentSector;
                    }
                    $currentSector = [
                        'icao' => $lineSplit[0],
                        'isOceanic' => $lineSplit[1],
                        'isExtension' => $lineSplit[2],
                        'points' => [],
                    ];
                } else {
                    if ('' != $line) {
                        $currentSector['points'][] = [$lineSplit[0], $lineSplit[1]];
                    }
                }
            }
            if (!empty($currentSector)) {
                $sectors[] = $currentSector;
            }

            $this->info('Total general sector count: '.sizeof($sectors));
            $output['general'] = $sectors;
        } else {
            $this->error('Unable to locate definition file at: '.$this->defFilePath);
        }
        //
        // Build sector borders based on sct data
        if (Storage::exists($this->sctFilePath)) {
            $this->info('Reading sct sector file data');
            $sctFile = \Storage::get($this->sctFilePath);
            $sectors = [];
            $currentSector = '';
            $coordinates = [];
            foreach (explode("\r\n", $sctFile) as $key => $line) {
                if ('' !== $line) {
                    $lineSplit = preg_split('/[\s]+/', $line);
                    if (!\Illuminate\Support\Str::startsWith($lineSplit[0], 'ED')) {
                        continue;
                    }
                    if ($lineSplit[0] !== $currentSector) {
                        $sectors[$currentSector] = $coordinates;
                        $currentSector = $lineSplit[0];
                        $coordinates = [];
                    } else {
                        if (0 == sizeof($coordinates)) {
                            $coordinates[] = [$this->convertDMSToDecimal($lineSplit[1]), $this->convertDMSToDecimal($lineSplit[2])];
                            $coordinates[] = [$this->convertDMSToDecimal($lineSplit[3]), $this->convertDMSToDecimal($lineSplit[4])];
                        } else {
                            $coordinates[] = [$this->convertDMSToDecimal($lineSplit[3]), $this->convertDMSToDecimal($lineSplit[4])];
                        }
                    }
                }
            }
            $sectors[$currentSector] = $coordinates;

            $output['sectors'] = $sectors;
        }
        //
        //  Build local sectors from local .ese file
        if (Storage::exists($this->eseFilePath)) {
            $this->info('Started reading from .ese file');
            $eseFile = Storage::get($this->eseFilePath);
            $positions = [];
            $airspaces = [];
            $currentSection = null;
            $currentAirspace = null;
            $currentSector = null;
            foreach (explode("\n", $eseFile) as $key => $line) {
                $line = str_replace("\r", '', $line);
                if ('' == $line) {
                    continue;
                }
                // Switch sections
                if ('[POSITIONS]' === $line) {
                    // start with positions
                    $currentSection = 'positions';
                    $this->info('Started reading positions from .ese file');
                }
                if ('[AIRSPACE]' === $line) {
                    $currentSection = 'airspace';
                    $this->info('Started reading airspaces from .ese file');
                }

                // Read positions
                if ('positions' === $currentSection) {
                    if ('[POSITIONS]' == $line) {
                        continue;
                    }
                    // EDWW_A_CTR:Bremen Radar:123.920:WA:A:EDWW:CTR:::2450:2477:N053.14.15.580:E009.14.23.499
                    // <name of position>:<radio callsign>:<frequency>:<identifier>:<middle letter>:<prefix>:<suffix>:<not used>:<not used>:
                    // <A code start of range>:<A code end of range>[:<VIS center1 latitude>:<VIS center1 longitude>[: ... ]]
                    $split = explode(':', $line);
                    if (sizeof($split) < 12) {
                        // $this->info($line);
                        $positions[$split[3]] = [
                            'name' => $split[0],
                            'callsign' => $split[1],
                            'frequency' => $split[2],
                            'lat' => '',
                            'lon' => '',
                        ];
                    } else {
                        $positions[$split[3]] = [
                            'name' => $split[0],
                            'callsign' => $split[1],
                            'frequency' => $split[2],
                            'lat' => $split[11],
                            'lon' => $split[12],
                        ];
                    }
                }
                // Read airspace
                if ('airspace' == $currentSection) {
                    $split = explode(':', $line);
                    if (sizeof($split) >= 2) {
                        if ('SECTOR' == $split[0]) {
                            if (null != $currentAirspace) {
                                $airspaces[$currentAirspace['name']] = $currentAirspace;
                                $currentAirspace = null;
                            }
                            $currentAirspace = [
                                'name' => str_replace('·', '_', $split[1]),
                                'lowerLimit' => $split[2],
                                'upperLimit' => $split[3],
                            ];
                        }
                        if (null != $currentAirspace && 'OWNER' == $split[0]) {
                            $length = sizeof($split); // Get the amount of owners HIGH -> LOW
                            $ownership = [];
                            for ($i = 1; $i < $length; ++$i) {
                                $ownership[$i] = $split[$i];
                            }
                            $currentAirspace['owners'] = $ownership;
                        }
                    }
                }
            }
            if (null !== $currentAirspace) {
                $airspaces[$currentAirspace['name']] = $currentAirspace;
                $currentAirspace = null;
            }
            $output['vatger'] = [
                'positions' => $positions,
                'airspace' => $airspaces,
            ];
            Storage::put($this->sectorOutputFile, json_encode($output, JSON_PRETTY_PRINT));
            $this->info('Finished reading .ese file.');
        }
    }

    private function convertDMSToDecimal($latlng)
    {
        $valid = false;
        $decimal_degrees = 0;
        $degrees = 0;
        $minutes = 0;
        $seconds = 0;
        $direction = 1;
        // Determine if there are extra periods in the input string
        $num_periods = substr_count($latlng, '.');
        if ($num_periods > 1) {
            $temp = preg_replace('/\./', ' ', $latlng, $num_periods - 1); // replace all but last period with delimiter
            $temp = trim(preg_replace('/[a-zA-Z]/', '', $temp)); // when counting chunks we only want numbers
            $chunk_count = count(explode(' ', $temp));
            if ($chunk_count > 2) {
                $latlng = preg_replace('/\./', ' ', $latlng, $num_periods - 1); // remove last period
            } else {
                $latlng = str_replace('.', ' ', $latlng); // remove all periods, not enough chunks left by keeping last one
            }
        }

        // Remove unneeded characters
        $latlng = trim($latlng);
        $latlng = str_replace('º', ' ', $latlng);
        $latlng = str_replace('°', ' ', $latlng);
        $latlng = str_replace("'", ' ', $latlng);
        $latlng = str_replace('"', ' ', $latlng);
        $latlng = str_replace('  ', ' ', $latlng);
        $latlng = substr($latlng, 0, 1).str_replace('-', ' ', substr($latlng, 1)); // remove all but first dash
        if ('' != $latlng) {
            // DMS with the direction at the start of the string
            if (preg_match("/^([nsewoNSEWO]?)\s*(\d{1,3})\s+(\d{1,3})\s*(\d*\.?\d*)$/", $latlng, $matches)) {
                $valid = true;
                $degrees = intval($matches[2]);
                $minutes = intval($matches[3]);
                $seconds = floatval($matches[4]);
                if ('S' == strtoupper($matches[1]) || 'W' == strtoupper($matches[1])) {
                    $direction = -1;
                }
            } elseif (preg_match(
                // DMS with the direction at the end of the string
                "/^(-?\d{1,3})\s+(\d{1,3})\s*(\d*(?:\.\d*)?)\s*([nsewoNSEWO]?)$/",
                $latlng,
                $matches
            )
            ) {
                $valid = true;
                $degrees = intval($matches[1]);
                $minutes = intval($matches[2]);
                $seconds = floatval($matches[3]);
                if ('S' == strtoupper($matches[4]) || 'W' == strtoupper($matches[4]) || $degrees < 0) {
                    $direction = -1;
                    $degrees = abs($degrees);
                }
            }
            if ($valid) {
                // A match was found, do the calculation
                $decimal_degrees = ($degrees + ($minutes / 60) + ($seconds / 3600)) * $direction;
            } else {
                // Decimal degrees with a direction at the start of the string
                if (preg_match("/^([nsewNSEW]?)\s*(\d+(?:\.\d+)?)$/", $latlng, $matches)) {
                    $valid = true;
                    if ('S' == strtoupper($matches[1]) || 'W' == strtoupper($matches[1])) {
                        $direction = -1;
                    }
                    $decimal_degrees = $matches[2] * $direction;
                } elseif (preg_match("/^(-?\d+(?:\.\d+)?)\s*([nsewNSEW]?)$/", $latlng, $matches)) {
                    // Decimal degrees with a direction at the end of the string
                    $valid = true;
                    if ('S' == strtoupper($matches[2]) || 'W' == strtoupper($matches[2]) || $degrees < 0) {
                        $direction = -1;
                        $degrees = abs($degrees);
                    }
                    $decimal_degrees = $matches[1] * $direction;
                }
            }
        }
        if ($valid) {
            return $decimal_degrees;
        } else {
            return false;
        }
    }
}
