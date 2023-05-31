<?php

namespace App\Http\Controllers\Administration\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SectorfileController extends Controller
{

	// private $_localPath = "temp/".$this->account->id."/"; // For production
	private $_localPath = "temp/testing/"; // For local testing ONLY

	private	$_airacFile = "airac.zip";
	private $_customFile = "custom.zip";
	private $_customFile2 = "custom2.zip";
    
    /**
     * Download a given sectorfile url
     * 
     * @param  [type] $sectorfileUrl [description]
     * @return [type]                [description]
     */
	function downloadSectorFile($sectorfileUrl) {
		$curlHandler = curl_init();
		curl_setopt($curlHandler, CURLOPT_URL, $sectorfileUrl);
		curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(
			"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36",
			"Referer: http://files.aero-nav.com/EDXX",
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
			"Accept-Encoding: gzip, deflate"
		));

		$downloadedData = curl_exec($curlHandler);

		curl_close($curlHandler);

		Storage::put($this->_localPath.$this->_airacFile, $downloadedData);
	}

	/**
	 * Unzip the downloaded airac and uploaded custom
	 * sectorfiles
	 * 
	 * @return [type] [description]
	 */
	private function unzipFiles() {
		$unzipPathAirac = $this->_localPath.'airac';
		$unzipPathCustom = $this->_localPath.'custom';
		$unzipPathCustom2 = $this->_localPath.'custom2';

		if(File::exists(storage_path('app').'/'.$this->_localPath.$this->_airacFile)) {
			$zip = new \ZipArchive;
			$res = $zip->open(storage_path('app').'/'.$this->_localPath.$this->_airacFile);
			if($res === TRUE) {
				$zip->extractTo(storage_path('app').'/'.$unzipPathAirac);
				$zip->close();
			}
		}

		if(File::exists(storage_path('app').'/'.$this->_localPath.$this->_customFile)) {
			$zip = new \ZipArchive;
			$res = $zip->open(storage_path('app').'/'.$this->_localPath.$this->_customFile);
			if($res === TRUE) {
				$zip->extractTo(storage_path('app').'/'.$unzipPathCustom);
				$zip->close();
			}
		}

		if(File::exists(storage_path('app').'/'.$this->_localPath.$this->_customFile2)) {
			$zip = new \ZipArchive;
			$res = $zip->open(storage_path('app').'/'.$this->_localPath.$this->_customFile2);
			if($res === TRUE) {
				$zip->extractTo(storage_path('app').'/'.$unzipPathCustom2);
				$zip->close();
			}
		}
	}

	/**
	 * Parse an sct file to a data holding array
	 * 
	 * @param  [type]  $filePath [description]
	 * @param  boolean $isCustom [description]
	 * @return [type]            [description]
	 */
	private function parseSectorFile($filePath, $isCustom = false) {
		$result = array(
			'info' => [],
			'colors' => [],
			'vor' => [],
			'ndb' => [],
			'fixes' => [],
			'airport' => [],
			'runway' => [],
			'sid' => [],
			'star' => [],
			'artcc' => [],
			'artcc low'	=> [],
			'artcc high' => [],
			'geo' => [],
			'regions' => [],
			'airway high' => [],
			'airway low' => [],
		);

		$activeSection = null;
		$lastLine = null;
		$currentSID = [];
		$currentSTAR = [];
		$currentARTCC = [];
		$currentGEO = [];
		$currentRegion = null;
		$currentRegionIterator = 0;

		foreach (file($filePath) as $line) {
			$line = trim($line);
			if(strlen($line) == 0) continue;

			switch ($line) {
				case '[INFO]':
					$activeSection = 'info';
					break;
				case '[VOR]':
					$activeSection = 'vor';
					break;
				case '[NDB]':
					$activeSection = 'ndb';
					break;
				case '[FIXES]':
					$activeSection = 'fixes';
					break;
				case '[AIRPORT]':
					$activeSection = 'airport';
					break;
				case '[RUNWAY]':
					$activeSection = 'runway';
					break;
				case '[SID]':
					$activeSection = 'sid';
					break;
				case '[STAR]':
					$activeSection = 'star';
					break;
				case '[ARTCC HIGH]':
					// $activeSection = 'artcc high';
					$activeSection = 'artcc';
					break;
				case '[ARTCC]':
					$activeSection = 'artcc';
					break;
				case '[ARTCC LOW]':
					// $activeSection = 'artcc low';
					$activeSection = 'artcc';
					break;
				case '[GEO]':
					$activeSection = 'geo';
					break;
				case '[REGIONS]':
					$activeSection = 'regions';
					break;
				case '[HIGH AIRWAY]':
					$activeSection = 'airway high';
					break;
				case '[LOW AIRWAY]':
					$activeSection = 'airway low';
					break;
				default:
					# code...
					break;
			}

			// Work with the line
			if(Str::startsWith($line, '[')) continue; // Skip Section Markers for further parsing
			
			if(Str::startsWith($line, '#define')) {
				$activeSection = null; // Colors have a own section in our data array
				// This line defines a color
				$ce = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
				$result['colors'][$ce[1]] = $ce[2];
			}

			$ls = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);

			switch ($activeSection) {
				case 'info':
					if(!$isCustom)
						$result['info'][] = $line;
					break;
				case 'vor':
					$result['vor'][$ls[0]] = ['freq' => $ls[1], 'lat' => $ls[2], 'lon' => $ls[3]];
					break;
				case 'ndb':
					$result['ndb'][$ls[0]] = ['freq' => $ls[1], 'lat' => $ls[2], 'lon' => $ls[3]];
					break;
				case 'fixes':
					$result['fixes'][$ls[0]] = ['lat' => $ls[1], 'lon' => $ls[2]];
					break;
				case 'airport':
					$result['airport'][$ls[0]] = ['twrfreq' => $ls[1],'lat' => $ls[2], 'lon' => $ls[3], 'as' => $ls[4]];
					break;
				case 'runway':
					$result['runway'][$ls[8]][] = $line;
					break;
				case 'sid':
					if(sizeof($ls) == 8) {
						$currentSID = ['icao' => $ls[0], 'rwy' => $ls[2], 'id' => $ls[3]];
						$result['sid'][$ls[0]][$ls[2]][$ls[3]][] = ['lat_from' => $ls[4], 'lon_from' => $ls[5], 'lat_to' => $ls[6], 'lon_to' => $ls[7]];
					} else {
						$result['sid'][$currentSID['icao']][$currentSID['rwy']][$currentSID['id']][] = ['lat_from' => $ls[0], 'lon_from' => $ls[1], 'lat_to' => $ls[2], 'lon_to' => $ls[3]];
					}
					break;
				case 'star':
					// Filter out holdings for now
					if($ls[sizeof($ls) - 1] == 'COLOR_Holding') break;
					// Parse actual stars
					if(sizeof($ls) == 4) {
						// Append coordinates to current active STAR
						$result['star'][$currentSTAR['icao']][$currentSTAR['id']][] = [
							'lat_from' => $ls[0],
							'lon_from' => $ls[1],
							'lat_to' => $ls[2],
							'lon_to' => $ls[3],
						];
					} else {
						// New STAR found
						$starIdentIndices = sizeof($ls) - 4; // Last for line split items are coordinates
						$starId = '';
						for($i = 1; $i < $starIdentIndices; $i++)
							$starId.= ' '.$ls[$i];
						$currentSTAR = ['icao' => $ls[0], 'id' => trim($starId)];
						$result['star'][$currentSTAR['icao']][$currentSTAR['id']][] = [
							'lat_from' => $ls[sizeof($ls) - 4],
							'lon_from' => $ls[sizeof($ls) - 3],
							'lat_to' => $ls[sizeof($ls) - 2],
							'lon_to' => $ls[sizeof($ls) - 1],
						];
					}
					break;
				case 'artcc':
					// If a color is defined for the airspace we need to extract that
					if(sizeof($ls) > 4) {
						// Maybe color definition at the end or artcc name at the beginning
						if(preg_match('/^[WE]{1}[0-9]{3}\.[0-9]{2}\.[0-9]{2}\.[0-9]{3}/', $ls[sizeof($ls) - 1])) {
							// Last index of the line split is a coordinate
							$artccIdentIndices = sizeof($ls) - 4;
							$artccIdent = '';
							for($i = 0; $i < $artccIdentIndices; $i++)
								$artccIdent.= ' '.$ls[$i];
							$currentARTCC = ['id' => trim($artccIdent)];
							$result['artcc'][$currentARTCC['id']][] = [
								'lat_from' => $ls[sizeof($ls) - 4],
								'lon_from' => $ls[sizeof($ls) - 3],
								'lat_to' => $ls[sizeof($ls) - 2],
								'lon_to' => $ls[sizeof($ls) - 1],
							];
						} else {
							// Last index is a color definition
							$artccIdentIndices = sizeof($ls) - 5;
							if($artccIdentIndices >= 1) {
								$artccIdent = '';
								for($i = 0; $i < $artccIdentIndices; $i++)
									$artccIdent.= ' '.$ls[$i];
								$currentARTCC = ['id' => trim($artccIdent), 'color' => $ls[sizeof($ls) - 1]];
							} else {
								$currentARTCC['color'] = $ls[sizeof($ls) - 1];
							}
							$result['artcc'][$currentARTCC['id']][] = [
								'lat_from' => $ls[sizeof($ls) - 5],
								'lon_from' => $ls[sizeof($ls) - 4],
								'lat_to' => $ls[sizeof($ls) - 3],
								'lon_to' => $ls[sizeof($ls) - 2],
							];
						}
					} else {
						$result['artcc'][$currentARTCC['id']][] = [
							'lat_from' => $ls[0],
							'lon_from' => $ls[1],
							'lat_to' => $ls[2],
							'lon_to' => $ls[3],
						];
					}
					break;
				case 'geo':
					if(!Str::startsWith($line, ';')) {
						if(sizeof($ls) > 5) {
							// New section
							$geoIndices = sizeof($ls) - 5;
							$geoIdent = '';
							for($i = 0; $i < $geoIndices; $i++) {
								$geoIdent.= ' '.$ls[$i];
							}
							$currentGEO['id'] = trim($geoIdent);
						}
						$result['geo'][$currentGEO['id']]['coords'][] = [
							'lat_from' => $ls[sizeof($ls) - 5],
							'lon_from' => $ls[sizeof($ls) - 4],
							'lat_to' => $ls[sizeof($ls) - 3],
							'lon_to' => $ls[sizeof($ls) - 2],
							'color' => $ls[sizeof($ls) - 1],
						];
					}
					break;
				case 'regions':
					if(!Str::startsWith($line, ';')) {
						if(Str::startsWith($line, 'REGIONNAME')) {
							$rn = '';
							for($i = 1; $i < sizeof($ls); $i++) {
								$rn.= ' '.$ls[$i];
							}
							$rn = trim($rn);
							if($currentRegion === null) {
								// New to this section
								$currentRegionIterator = 0;
								$currentRegion['name'] = $rn;
								$currentRegion['regions'] = array();
							} else {
								if($currentRegion['name'] != $rn) {
									$result['regions'][$currentRegion['name']] = $currentRegion['regions'];
									$currentRegion['name'] = $rn;
									$currentRegion['regions'] = array();
									$currentRegionIterator = 0;
								}
							}
						} else {
							if(sizeof($ls) == 3) {
								$currentRegionIterator++;
								$currentRegion['regions'][$currentRegionIterator]['color'] = $ls[0];
								$currentRegion['regions'][$currentRegionIterator]['coords'][] = ['lat' => $ls[1], 'lon' => $ls[2]];
							}
							if(sizeof($ls) == 2) {
								$currentRegion['regions'][$currentRegionIterator]['coords'][] = ['lat' => $ls[0], 'lon' => $ls[1]];
							}
						}
					}
					break;
				case 'airway high':
					$result['airway high'][$ls[0]][] = $ls[1].' '.$ls[2].' '.$ls[3].' '.$ls[4];
					break;
				case 'airway low':
					$result['airway low'][$ls[0]][] = $ls[1].' '.$ls[2].' '.$ls[3].' '.$ls[4];
					break;
				default:
					break;
			}


			$lastLine = $line;
		}

		if(!array_key_exists($currentRegion['name'], $result['regions'])) {
			$result['regions'][$currentRegion['name']] = $currentRegion['regions'];
		}

		// Clear buffers
		unset($currentSID);
		unset($currentSTAR);
		unset($currentARTCC);
		unset($currentGEO);
		unset($currentRegion);

		return $result;
	}

	private function buildSectorfile($airac, $custom) {
		// Cycle through the sections in the airac array
		// and combine it with the custom array if things are altered
		// or non existent in the airac array
		
		$result = $airac;

		foreach ($custom as $section => $data) {
			foreach ($data as $key => $value) {
				if(!array_key_exists($key, $result[$section])) {
					$result[$section][$key] = $value;
				} else {
					if($result[$section][$key] != $value) {
						$result[$section][$key] = $value;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Combine the airac and custom sectorfile sets
	 * 
	 * @param  boolean $nav     [description]
	 * @param  boolean $geo     [description]
	 * @param  boolean $regions [description]
	 * @param  boolean $colors  [description]
	 * @return [type]           [description]
	 */
	private function combineFiles($nav = true, $geo = false, $regions = false, $colors = false) {
		// Go through the files and combine sections that are marked to be combined
		$combinedSectorFile = '';
		$combinedExtensionFile = '';
		
		// Grab custom and airac .sct and .ese files
		$airacDirectory = Storage::files($this->_localPath.'airac');
		$customDirectory = Storage::files($this->_localPath.'custom');
		$customDirectory2 = Storage::files($this->_localPath.'custom2');

		$airacSectorFile = false;
		$airacSectorExtensionFile = false;

		foreach ($airacDirectory as $f) {
			$ext = explode('.', $f)[1];
			if($ext == 'sct') {
				$airacSectorFile = $f;
			}
			if($ext == 'ese') {
				$airacSectorExtensionFile = $f;
			}
		}

		$customSectorFile = false;
		$customSectorExtensionFile = false;

		foreach ($customDirectory as $f) {
			$ext = explode('.', $f)[1];
			if($ext == 'sct') {
				$customSectorFile = $f;
			}
			if($ext == 'ese') {
				$customSectorExtensionFile = $f;
			}
		}

		$customSectorFile2 = false;
		$customSectorExtensionFile2 = false;

		foreach ($customDirectory2 as $f) {
			$ext = explode('.', $f)[1];
			if($ext == 'sct') {
				$customSectorFile2 = $f;
			}
			if($ext == 'ese') {
				$customSectorExtensionFile2 = $f;
			}
		}

		if($airacSectorFile)
			$sctAirac = $this->parseSectorFile(storage_path('app').'/'.$airacSectorFile);
		if($customSectorFile)
			$sctCustom = $this->parseSectorFile(storage_path('app').'/'.$customSectorFile, ($airacSectorFile) ? true : false);
		if($customSectorFile2)
			$sctCustom2 = $this->parseSectorFile(storage_path('app').'/'.$customSectorFile2, true);

		// Combine the results to a single sct file
		$result = false;
		if($airacSectorFile && $customSectorFile)
			$result = $this->buildSectorfile($sctAirac, $sctCustom);
		if($result && $customSectorFile2)
			$result = $this->buildSectorfile($result, $sctCustom2);
		if(!$result && $customSectorFile && $customSectorFile2)
			$result = $this->buildSectorfile($sctCustom, $sctCustom2);

		return $result;
	}

	private function generateCombinedSectorfile($sctData) {
		$sctOutput = '; ==================================================<br />';
		$sctOutput.= '; VATSIM GERMANY SECTORFILE COMBINER<br />';
		$sctOutput.= '; This sectorfile has been generated by VATSIM Germany Sectorfile Combiner.<br />';
		$sctOutput.= '; This file MUST NOT be distributed to anyone outside the VATSIM Network.<br />';
		$sctOutput.= '; For use on the VATSIM Network ONLY.<br />';
		$sctOutput.= '; For FLIGHTSIMULATION use ONLY.<br />';
		$sctOutput.= '; ==================================================<br /><br /><br /><br /><br />';
		// Build Info Section
		$sctOutput.= '; ==================================================<br />';
		$sctOutput.= '[INFO]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['info'] as $infoline) {
			$sctOutput.= $infoline.'<br />';
		}
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '; Color Definitions<br />';
		$sctOutput.= '; (BLUE x 65536) + (GREEN x 256) + RED<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['colors'] as $cn => $cv) {
			$sctOutput.= '#define '.$cn.' '.$cv.'<br />';
		}
		// VOR Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[VOR]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['vor'] as $ident => $data) {
			$sctOutput.= $ident.' '.$data['freq'].' '.$data['lat'].' '.$data['lon'].'<br />';
		}
		// NDB Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[NDB]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['ndb'] as $ident => $data) {
			$sctOutput.= $ident.' '.$data['freq'].' '.$data['lat'].' '.$data['lon'].'<br />';
		}
		// FIXES Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[FIXES]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['fixes'] as $ident => $data) {
			$sctOutput.= $ident.' '.$data['lat'].' '.$data['lon'].'<br />';
		}
		// AIRPORT Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[AIRPORT]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['airport'] as $icao => $data) {
			$sctOutput.= $icao.' '.$data['twrfreq'].' '.$data['lat'].' '.$data['lon'].' '.$data['as'].'<br />';
		}
		// RUNWAY Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[RUNWAY]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['runway'] as $icao => $data) {
			$sctOutput.= '; '.$icao.'<br />';
			foreach ($data as $rwyLine) {
				$sctOutput.= $rwyLine.'<br />';
			}
		}
		// SID / STAR SECTOR FILE FORMAT DEFINITION
		// An individual diagram consists of one or more lines in the sector file. Each line defines a single line segment in the diagram. The first line of the diagram definition contains the name of the diagram. The name field must be exactly 26 characters in length. If the name of the diagram is shorter than 26 characters, trailing spaces must be added to fill the 26 characters. After the first 26 characters, there can be one or more optional spaces, followed by the latitude and longitude of the start and end points of the line segment, followed by an optional color name or value. If no color name or value is given, the diagram will be drawn using the default SID or STAR color as defined in the radar client settings.
		// Subsequent lines in a diagram definition must have 26 spaces, followed by the latitude and longitude for the start and end points of the current line segment, followed by an optional color name or value. In other words, subsequent segment definitions are identical to the starting segment definition, except that only the starting segment contains the name of the diagram in the first 26 characters. VRC will continue reading lines and adding them to the current diagram definition until it encounters the start of a new diagram (signified by a name present in the first 26 characters) or the start of a new section.
		// SID Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[SID]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['sid'] as $icao => $runways) {
			$sctOutput.= '; '.$icao.'<br />';
			foreach ($runways as $runway => $sids) {
				$sctOutput.= '; '.$runway.'<br />';
				foreach ($sids as $sid => $coords) {
					$firstLine = true; // New SID...
					$sidIdentifier = $icao.' '.$sid;
					foreach ($coords as $coord) {
						if($firstLine) {
							$sctOutput.= $sidIdentifier;
							for($i = 0; $i < 26 - strlen($sidIdentifier) + 1; $i++) { // 26 chars ident + 1 additional whitespace
								$sctOutput.= '&nbsp;';
							}
							$sctOutput.= $coord['lat_from'].' '.$coord['lon_from'].' '.$coord['lat_to'].' '.$coord['lon_to'].'<br />';
							$firstLine = false;
						} else {
							for($i = 0; $i < 27; $i++) { // 26 + 1 whitespaces
								$sctOutput.= '&nbsp;';
							}
							$sctOutput.= $coord['lat_from'].' '.$coord['lon_from'].' '.$coord['lat_to'].' '.$coord['lon_to'].'<br />';
						}
					}
				}
			}
		}
		// STAR Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[STAR]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['star'] as $icao => $stars) {
			$sctOutput.= '; '.$icao.'<br />';
			foreach ($stars as $star => $coords) {
				$firstLine = true; // New STAR...
				$starIdentifier = $icao.' '.$star;
				foreach ($coords as $coord) {
					if($firstLine) {
						$sctOutput.= $starIdentifier;
						for($i = 0; $i < 26 - strlen($starIdentifier) + 1; $i++) { // 26 chars ident + 1 additional whitespace
							$sctOutput.= '&nbsp;';
						}
						$sctOutput.= $coord['lat_from'].' '.$coord['lon_from'].' '.$coord['lat_to'].' '.$coord['lon_to'].'<br />';
						$firstLine = false;
					} else {
						for($i = 0; $i < 27; $i++) { // 26 + 1 whitespaces
							$sctOutput.= '&nbsp;';
						}
						$sctOutput.= $coord['lat_from'].' '.$coord['lon_from'].' '.$coord['lat_to'].' '.$coord['lon_to'].'<br />';
					}
				}
			}
		}
		// ARTCC Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[ARTCC]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['artcc'] as $id => $coords) {
			if(Str::startsWith($id, 'Restricted')) continue;

			$sctOutput.= '; '.$id.'<br />';
			$firstLine = true;
			foreach ($coords as $coord) {
				if($firstLine) {
					$sctOutput.= $id;
					for($i = 0; $i < 26 - strlen($id) + 1; $i++) { // 26 chars ident + 1 additional whitespace
						$sctOutput.= '&nbsp;';
					}
					$sctOutput.= $coord['lat_from'].' '.$coord['lon_from'].' '.$coord['lat_to'].' '.$coord['lon_to'].'<br />';
					$firstLine = false;
				} else {
					for($i = 0; $i < 27; $i++) { // 26 + 1 whitespaces
						$sctOutput.= '&nbsp;';
					}
					$sctOutput.= $coord['lat_from'].' '.$coord['lon_from'].' '.$coord['lat_to'].' '.$coord['lon_to'].'<br />';
				}
			}
		}
		// GEO Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[GEO]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['geo'] as $id => $geo) {
			if(strlen($id) >= 26) {
				// Trim it down to max 26 chars
				$gid = substr($id, 0, 26);
			} else {
				$gid = $id;
			}
			$sctOutput.= '; '.$gid.'<br />';
			$firstLine = true;
			foreach ($geo['coords'] as $coord) {
				if($firstLine) {
					$sctOutput.= $gid;
					for($i = 0; $i < 26 - strlen($gid) + 1; $i++) { // 26 chars ident + 1 additional whitespace
						$sctOutput.= '&nbsp;';
					}
					$sctOutput.= $coord['lat_from'].' '.$coord['lon_from'].' '.$coord['lat_to'].' '.$coord['lon_to'].' '.$coord['color'].'<br />';
					$firstLine = false;
				} else {
					for($i = 0; $i < 27; $i++) { // 26 + 1 whitespaces
						$sctOutput.= '&nbsp;';
					}
					$sctOutput.= $coord['lat_from'].' '.$coord['lon_from'].' '.$coord['lat_to'].' '.$coord['lon_to'].' '.$coord['color'].'<br />';
				}
			}
		}
		// REGIONS Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[REGIONS]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['regions'] as $id => $regions) {
			if(strlen($id) >= 26) {
				// Trim it down to max 26 chars
				$rid = substr($id, 0, 26);
			} else {
				$rid = $id;
			}
			$sctOutput.= '; Region '.$rid.'<br />';
			foreach ($regions as $region) {
				$firstLine = true;
				$sctOutput.= 'REGIONNAME '.$rid.'<br />';
				foreach($region['coords'] as $coord) {
					if($firstLine) {
						$sctOutput.= $region['color'];
						for($i = 0; $i < 26 - strlen($region['color']) + 1; $i++) { // 26 chars ident + 1 additional whitespace
							$sctOutput.= '&nbsp;';
						}
						$sctOutput.= $coord['lat'].' '.$coord['lon'].'<br />';
						$firstLine = false;
					} else {
						for($i = 0; $i < 27; $i++) { // 26 + 1 whitespaces
							$sctOutput.= '&nbsp;';
						}
						$sctOutput.= $coord['lat'].' '.$coord['lon'].'<br />';
					}
				}
			}
		}
		// AIRWAYS Section
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[HIGH AIRWAY]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['airway high'] as $airway => $points) {
			foreach ($points as $p) {
				$sctOutput.= $airway.' '.$p.'<br />';
			}
		}
		$sctOutput.= '<br /><br />; ==================================================<br />';
		$sctOutput.= '[LOW AIRWAY]<br />';
		$sctOutput.= '; ==================================================<br />';
		foreach ($sctData['airway low'] as $airway => $points) {
			foreach ($points as $p) {
				$sctOutput.= $airway.' '.$p.'<br />';
			}
		}
		return $sctOutput;
	}

	/**
	 * Called from the web to upload a custom sectorfile set
	 * and then combine it with a given airac cycle sectorfile set.
	 * 
	 * @param  Request $request  [description]
	 * @return [type]            [description]
	 */
	function combineSectorFiles(Request $request) {

		$validated = $request->validate([
			'airacUrl' => 'url|nullable',
			'sector_one' => 'required|file',
			'sector_two' => 'file'
		]);

		if($request->has('airacUrl')) {
			if(!Storage::exists($this->_localPath.$this->_airacFile)) {
				$this->downloadSectorFile($validated['airacUrl']);
			}
		}

		// Store the custom uploaded sector kit
		$request->file('sector_one')->storeAs($this->_localPath, $this->_customFile);

		if($request->hasFile('sector_two')) {
			$request->file('sector_two')->storeAs($this->_localPath, $this->_customFile2);
		}

		$this->unzipFiles();

		$result = $this->combineFiles();

		Storage::deleteDirectory($this->_localPath);

		return $this->generateCombinedSectorfile($result);
	}

	/**
	 * Function to "parse" the ENR-2.1 PDF from EAD - DE
	 * 
	 * The output will depend on the $mode switch
	 * 
	 * @param Request $request
	 * @param mixed $mode The mode Switch
	 * @return Response
	 */
	function buildFromAIP(Request $request, $mode) {
		// $aip = new \App\Models\Navigation\AIP('navigation/ED_ENR_2_1_en_2020-08-13.pdf');
		// $aip->parse();

		// if($mode == 'sct')
		// 	$output = $aip->buildSct();
		// elseif($mode == 'ese')
		// 	$output = $aip->buildEse();
		// else
		// 	$output = 'Unbekannter Modus. sct oder ese sind erlaubt.';

		// return response($output, 200)
		// 	->header('Content-Type', 'text/plain');
		return "Aufgrund von rechtlichen Bedenken ist dieses Feature seitens NAV-Germany gesperrt und bis zu einer abschließenden Klärung der Rechtsgrundlage nicht nutzbar.";
	}

	function test(Request $request) {
		return $this->viewMake('testing.sectorcombine');
	}

}
