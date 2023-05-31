<?php

namespace App\Http\Controllers\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SectordataController extends Controller
{
    protected $aliases = [
        'ADR_W_CTR' => [
            'LJLA', 'LDZO', 'LQSB', 'LAAA', 'LWSS', 'LYBA',
        ],
        'FTW_51_CTR' => [
            'KZFW',
        ],
        'ATL_43_CTR' => [
            'KZTL',
        ],
        'MTL_CTR' => [
            'CZUL',
        ],
        'BOS_CTR' => [
            'KZBW',
        ],
        'TOR_PI_CTR' => [
            'CZYZ',
        ],
        'CLE_CTR' => [
            'KZOB',
        ],
        'CHI_35_CTR' => [
            'KZAU',
        ],
        'MIA_46_CTR' => [
            'KZMA',
        ],
        'JAX_35_CTR' => [
            'KZJX',
        ],
        'IND_CTR' => [
            'KZID',
        ],
        'NY_CTR' => [
            'KZNY',
        ],
        'ASIA_W_FSS' => [
            'OAKX', 'OPLR', 'VIDF', 'VNSM', 'VEGF', 'OPKR', 'VABF', 'VRMF', 'VCCF', 'VOMF', 'VECD', 'VGFR',
        ],
        'ML-SNO_CTR' => [
            'YSNO', 'YWON', 'YYWE', 'YEKW', 'YHUM',
        ],
        'GUM_CTR' => [
            'PGZU',
        ],
        'LON_CTR' => [
            'EGTT-N', 'EGTT-C', 'EGTT-S', 'EGTT-W',
        ],
        'LON_SC_CTR' => [
            'EGTT-S', 'EGTT-C',
        ],
        'SCO_CTR' => [
            'EGPX-E', 'EGPX-W',
        ],
    ];

    protected $boundaryFile = 'navigation/sectors/fir_boundaries.json';

    protected $airportsDataSource = 'navigation/sectors/Airports.dat';

    protected $uirFile = 'navigation/sectors/UIR.dat';

    protected $boundaries = null;

    protected $uirs = [];

    protected $vaccPositions = null;

    protected $connectedClients = null;

    protected $connectedATC = [];

    protected $weekLength = 7 * 24 * 60 * 60;

    protected $vaccGerAtcHandles = [
        'ED', 'ETA', 'ETH', 'ETI', 'ETM', 'ETN', 'ETS',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->boundaries = json_decode(Storage::get($this->boundaryFile));

        $this->vaccPositions = $this->boundaries->vatger->positions;

        // Load UIR Data
        $uirFileContent = Storage::get($this->uirFile);
        foreach (explode("\n", $uirFileContent) as $line) {
            // ADR_E|Adria Radar|LYBA,LWSS,LAAA
            $split = explode('|', $line);
            $this->uirs[$split[0]] = $split[2];
        }

        if (Cache::has('network.data.connectedClients')) {
            $this->connectedClients = Cache::get('network.data.connectedClients');
        }

        if (null != $this->connectedClients) {
            foreach ($this->connectedClients->controllers as $controller) {
                // Active ATC found.. let's find out if it is germany or not
                foreach ($this->vaccPositions as $id => $pos) {
                    if ($pos->name == $controller->callsign) {
                        $this->connectedATC[$id] = $controller;
                    }
                }
            }
        }
    }

    /**
     * Get a single sector boundary matching the callsing.
     *
     * @param [type] $callsign [description]
     *
     * @return [type] [description]
     */
    public function getSector($callsign = null)
    {
        if (null == $callsign) {
            return json_encode([]);
        }

        if (!$this->_isEseSector($callsign)) {
            return $this->_getSimpleSector($callsign);
        } else {
            return $this->_getEseSector($callsign);
        }
    }

    /**
     * Load sector information from simple data.
     *
     * This is for any non vatger sector
     *
     * @param [type] $callsign [description]
     *
     * @return [type] [description]
     */
    private function _getSimpleSector($callsign)
    {
        if (array_key_exists($callsign, $this->aliases)) {
            $response['multiple'] = true;
            $boundryLines = [];
            foreach ($this->boundaries->general as $key => $fir) {
                foreach ($this->aliases[$callsign] as $subsector) {
                    if ($fir->icao == $subsector) {
                        $boundryLines[] = $fir->points;
                    }
                }
            }
            $response['points'] = $boundryLines;

            return json_encode($response);
        } elseif (array_key_exists(explode('_', $callsign)[0], $this->uirs)) {
            $response['multiple'] = true;
            $sectors = [];

            $firs = explode(',', $this->uirs[explode('_', $callsign)[0]]);
            foreach ($firs as $fir) {
                foreach ($this->boundaries->general as $k => $f) {
                    if ($f->icao == $fir) {
                        $sectors[] = $f->points;
                    }
                }
            }

            $response['points'] = $sectors;

            return json_encode($response);
        } else {
            $possibleCandidates = [];
            $possibleCandidates[] = $callsign;
            $possibleCandidates[] = explode('_', $callsign)[0].'-'.explode('_', $callsign)[1];
            $possibleCandidates[] = explode('_', $callsign)[0];
            foreach ($this->boundaries->general as $k => $fir) {
                foreach ($possibleCandidates as $pc) {
                    if ($fir->icao == $pc) {
                        $response['multiple'] = false;
                        $response['points'] = $fir->points;

                        return $response;
                    }
                }
            }
        }

        return json_encode(['multiple' => false, 'points' => []]);
    }

    /**
     * Get sector data from the ese section
     * then make some sense of it and generate sector boundary.
     *
     * @param [type] $callsign [description]
     *
     * @return [type] [description]
     */
    private function _getEseSector($callsign)
    {
        foreach ($this->vaccPositions as $ident => $position) {
            if ($position->name == $callsign) {
                // We have a matching position. Now find all sectors that are somehow interesting
                $sectors = [];

                foreach ($this->boundaries->vatger->airspace as $airspace => $definition) {
                    if (!property_exists($definition, 'owners')) {
                        continue;
                    }
                    if (intval($definition->lowerLimit) < 5000 || intval($definition->upperLimit < 10500)) {
                        continue;
                    }
                    // If the airspace owners contains the position... add it
                    foreach ($definition->owners as $id => $cs) {
                        if ($cs == $ident) {
                            // Find the corresponding sector boundary in the sectors section
                            foreach ($this->boundaries->sectors as $sec => $coords) {
                                if ($airspace == $sec) {
                                    $sectors[] = $coords;
                                }
                            }
                        }
                    }
                }

                $response['multiple'] = true;
                $response['points'] = $sectors;

                return json_encode($response);
            }
        }
    }

    /**
     * Is the callsign within the vacc definitions.
     *
     * @param [type] $callsign [description]
     *
     * @return bool [description]
     */
    private function _isEseSector($callsign)
    {
        if (!in_array(substr($callsign, 0, 2), $this->vaccGerAtcHandles) && !in_array(substr($callsign, 0, 3), $this->vaccGerAtcHandles)) {
            return false;
        }
        foreach ($this->vaccPositions as $id => $pos) {
            if ($pos->name == $callsign) {
                return true;
            }
        }

        return false;
    }
}
