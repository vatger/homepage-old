<?php

namespace App\Models\Navigation;

use Illuminate\Support\Str;

class AIP
{
    
	private $_aipFile;

	private $_sectors = array();

	private $_skipOnWords = ['part', 'gebiet', 'territory', 'area', 'limit', 'grenze', 'Grenze', 'ft', 'MSL', 'INFORMATION', 'FL', 'APPROACH', 'DFS', 'AIP', 'Gebiet', 'LUFT', 'borde', 'exclusiv', 'Radius', 'radius', 'Limit', 'cent', 'auf', 'RADAR', 'entlang', 'richtung', 'inclusive', 'ausschließlich', 'deutsch', 'einschließlich', 'Kreis', '.', '123', 'ATS', 'IFR', 'coordi', 'Airspace', 'airspace', 'H24', 'EN', 'Effective', 'kHz', 'channel', 'gegen'];

	function __construct($file)
	{
		$this->_aipFile = $file;
	}

	public function parse() : void
	{
		$parser = new \Smalot\PdfParser\Parser();
		$pdf = $parser->parseFile(storage_path('app').'/'.$this->_aipFile);
		$pages = $pdf->getPages();

		$currentSector = array();

		$processingSector = false;

		for ($i = 0; $i < sizeof($pages); $i++) {

			$pageData = $pages[$i]->getTextArray();

			$lastCoordinateFoundAt = 0;


			for($j = 0; $j < sizeof($pageData); $j++) {

				if(preg_match("/N{1}\s?(\d{2}\s\d{2}\s\d{2}|\d{6})\s?E{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})/", $pageData[$j])) {
					
					if(!$processingSector) {
						if(isset($currentSector['name']) && isset($currentSector['coordinates'])) {
							$this->_sectors[] = $currentSector;
						}

						$currentSector['name'] = '';
						$currentSector['coordinates'] = '';

						$matches;
						preg_match("/N{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})\s?E{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})/", $pageData[$j], $matches, PREG_OFFSET_CAPTURE);

						if($matches[0][1] > 0) {
							$currentSector['name'] = substr($pageData[$j], 0, $matches[0][1] - 1);
							$currentSector['coordinates'] = substr($pageData[$j], $matches[0][1]);
						} else {
							if($lastCoordinateFoundAt != 0) {
								for($k = $j - 1; $k > $lastCoordinateFoundAt; $k--) {
									if(
										!preg_match("/N{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})\s?E{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})/", $pageData[$k])
										&& preg_match("/(EDD|ETN|ETS|EDUU|EDWW|EDYY|EDGG|EDMM|FIR|UIR|\(HX\)|CTR|Sector)/", $pageData[$k])
										&& Str::length($pageData[$k]) > 1
									) {
										$currentSector['name'] = $pageData[$k];
										if(!Str::contains($currentSector['name'], ['part', 'gebiet', 'territory', 'area']))
											break;
									}
								}
								$lastCoordinateFoundAt = 0;
							} else {
								$currentSector['name'] = $pageData[$j - 1];
							}
							$currentSector['coordinates'] = $pageData[$j];
						}
						// var_dump($currentSector['name']);
						$crawlBack = ($j >= 2) ? $j - 1 : $j;
						while($crawlBack > 1
							&& (Str::length($currentSector['name']) == 0
									|| Str::contains($currentSector['name'], $this->_skipOnWords)
									|| preg_match("/N{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})\s?E{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})/", $currentSector['name'])
								)
							)
						{
							$currentSector['name'] = $pageData[$crawlBack];
							$crawlBack--;
						}

						if(
							$crawlBack == 0 && Str::length($currentSector['name']) == 0
							|| Str::contains($currentSector['name'], $this->_skipOnWords)
							|| preg_match("/N{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})\s?E{1}\s?(\d{2,3}\s\d{2}\s\d{2}|\d{6,7})/", $currentSector['name'])
						) {
							$currentSector['name'] = $pageData[$j - 1];
						}

						// var_dump($currentSector['name']);

						if(Str::endsWith($pageData[$j], ['.', ';'])) {
							$lastCoordinateFoundAt = $j;
							$this->_sectors[] = $currentSector;
							$processingSector = false;
						} else {
							$processingSector = true;
						}
					} else {

						// Don't parse remarks
						if(preg_match("/(^\d{1}\.\s?|\d{1}\.\s\w+\sTWR)/", $pageData[$j])) {
							$lastCoordinateFoundAt = $j;
							$processingSector = false;
						}

						if(isset($currentSector['name']) && isset($currentSector['coordinates'])) {
							$currentSector['coordinates'] .= ' '.$pageData[$j];
						}

						if(Str::endsWith($pageData[$j], ['.', ';']) && $processingSector) {
							$lastCoordinateFoundAt = $j;
							$processingSector = false;
						}
					}

				} else {

					// Don't parse remarks
					if(preg_match("/(^\d{1}\.\s?|\d{1}\.\s\w+\sTWR)/", $pageData[$j])) {
						$lastCoordinateFoundAt = $j;
						$processingSector = false;
					}

					if(isset($currentSector['name']) && isset($currentSector['coordinates']) && $processingSector) {
						$currentSector['coordinates'] .= ' '.$pageData[$j];
					}

					if(Str::endsWith($pageData[$j], ['.', ';']) && $processingSector) {
						$lastCoordinateFoundAt = $j;
						$processingSector = false;
					}
				}


			}

		}
	}

	public function buildSct() : string {
		$sctOutput = "[ARTCC]\n";
		$lastSensfulSectorname = '';
		foreach ($this->_sectors as $sector) {

			if((preg_match("/((a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z){1}\)?|\/)/", $sector['name']) && Str::length($sector['name']) < 3) && $lastSensfulSectorname != '') {
				$sector['name'] = $lastSensfulSectorname.' '.$sector['name'];
			} else {
				$lastSensfulSectorname = $sector['name'];
			}

			$sctOutput .= "; ================================\n";
			$sctOutput .= "; ".$sector['name']."\n";
			$sctOutput .= "; ================================\n";
			$convertedCoordinates = $this->convertCoordinates($sector['coordinates'], 'sct');
			$coords = explode("\n", $convertedCoordinates);
			foreach ($coords as $c) {
				$cSplit = explode(" ", $c);
				$warning = false;
				foreach ($cSplit as $cs) {
					if(!preg_match("/(N{1}0{1}(4|5){1}|E{1}0{1}(05|06|07|08|09|10|11|12|13|14){1})/", $cs))
						$warning = true;
				}
				if($warning)
					$sctOutput .= $sector['name'].' '.$c." ; WARNING!!!\n";
				else
					$sctOutput .= $sector['name'].' '.$c."\n";
			}
		}
		return $sctOutput;
	}

	public function buildEse() : string {
		$eseOutput = "";
		foreach ($this->_sectors as $sector) {
			if((preg_match("/((a|b|c|d|e|f|g|h|i|j|k|l|m|n|o|p|q|r|s|t|u|v|w|x|y|z){1}\)?|\/)/", $sector['name']) && Str::length($sector['name']) < 3) && $lastSensfulSectorname != '') {
				$sector['name'] = $lastSensfulSectorname.' '.$sector['name'];
			} else {
				$lastSensfulSectorname = $sector['name'];
			}

			$eseOutput .= "; ================================\n";
			$eseOutput .= "; ".$sector['name']."\n";
			$eseOutput .= "; ================================\n";
			$eseOutput .= "SECTORLINE:".$sector['name']."\n";
			$convertedCoordinates = $this->convertCoordinates($sector['coordinates'], 'ese');
			$coords = explode("\n", $convertedCoordinates);
			foreach ($coords as $c) {
				$cSplit = explode(" ", $c);
				$warning = false;
				foreach ($cSplit as $cs) {
					if(!preg_match("/(N{1}0{1}(4|5){1}|E{1}0{1}(05|06|07|08|09|10|11|12|13|14){1})/", $cs))
						$warning = true;
				}
				if($warning)
					$eseOutput .= $c." ; WARNING!!!\n";
				else
					$eseOutput .= $c."\n";
			}
		}
		return $eseOutput;
	}

	private function convertCoordinates($coords, $format = 'sct')
	{

		$coords = str_replace(",", ".", $coords);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,"https://webtools.kusternet.ch/coordinatesimporter");
		curl_setopt($ch, CURLOPT_POST, 1);

		// In real life you should use something like:
		curl_setopt($ch, CURLOPT_POSTFIELDS, 
		         http_build_query(array(
		         	'text1' => $coords,
		         	'analyse' => 'Analyse',
		         	'format' => $format,
		         	'colorname' => ''
		         )));

		// Receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec($ch);

		curl_close ($ch);

		$coords = preg_split("/<textarea name=\"text2\" rows=\"40\" cols=\"90\" readonly=\"readonly\">/i", $server_output)[1];
		$coords = trim($coords);
		$coords = substr($coords, 0, strpos($coords, "</textarea>"));

		return $coords;
	}

}
