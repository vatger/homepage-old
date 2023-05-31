<?php

namespace App\Models\Navigation;

class SCT
{
    private $colors = [];

    private $raw;

    private $groundlayouts = [];

    public function __construct($r)
    {
        $this->raw = $r->Document;
        // $this->parse();
    }

    private function parseGeo($geo, $defaultStyle = 'COLOR_GEO')
    {
        // GEO FOUND
        if (isset($geo->LineString)) {
            $coords = explode(' ', $geo->LineString->coordinates);
            foreach ($coords as $key => $value) {
                $v = trim($value);
                if ('' != $v && !empty($v) && !ctype_space($v)) {
                    $coords[$key] = substr($v, 0, strlen($v) - 2);
                } else {
                    unset($coords[$key]);
                }
            }
            $g = [];
            $g['name'] = trim($geo->name);
            if (isset($geo->description)) {
                $g['style'] = ''.$geo->description;
            } else {
                $g['style'] = ''.$defaultStyle;
            }
            $g['coords'] = $coords;

            return $g;
        }
    }

    private function parseMultiGeo($geo, $defaultStyle = 'COLOR_GEO', $fname = 'Germany')
    {
        // MULTIGEO FOUND
        if (isset($geo->MultiGeometry)) {
            $returnGeo = [];
            $polygons = $geo->MultiGeometry;
            foreach ($polygons as $poly) {
                foreach ($poly->Polygon as $p) {
                    $coords = explode(' ', $p->outerBoundaryIs->LinearRing->coordinates);
                    foreach ($coords as $key => $value) {
                        $v = trim($value);
                        if ('' != $v && !empty($v) && !ctype_space($v)) {
                            $coords[$key] = substr($v, 0, strlen($v) - 2);
                        } else {
                            unset($coords[$key]);
                        }
                    }
                    $g = [];
                    $g['name'] = trim($fname);
                    $g['style'] = (isset($geo->description)) ? $geo->description : $defaultStyle;
                    $g['coords'] = $coords;

                    $returnGeo[] = $g;
                }
            }
            return $returnGeo;
        }
    }

    private function parseLabel($label, $defaultStyle = 'COLOR_LABEL')
    {
        if (isset($label->Point)) {
            $coords = substr($label->Point->coordinates, 0, strlen($label->Point->coordinates) - 2);
            $l = [];
            $l['name'] = trim($label->name);
            if (isset($label->description)) {
                $l['style'] = ''.$label->description;
            } else {
                $l['style'] = ''.$defaultStyle;
            }
            $l['coords'] = $coords;

            return $l;
        }
    }

    private function parseRegion($region, $defaultStyle = 'COLOR_REGION')
    {
        if (isset($region->Polygon)) {
            $coords = explode(' ', $region->Polygon->outerBoundaryIs->LinearRing->coordinates);
            foreach ($coords as $key => $value) {
                $v = trim($value);
                if ('' != $v && !empty($v) && !ctype_space($v)) {
                    $coords[$key] = substr($v, 0, strlen($v) - 2);
                } else {
                    unset($coords[$key]);
                }
            }
            $r = [];
            $r['name'] = trim($region->name);
            if (isset($region->description)) {
                $r['style'] = ''.$region->description;
            } else {
                $r['style'] = ''.$defaultStyle;
            }
            $r['coords'] = $coords;

            return $r;
        }
    }

    public function parse()
    {
        if (isset($this->raw->Folder)) {
            $folder = $this->raw->Folder;
            if(isset($folder->description)) {
                // Parse Color codes from description
                $desc = trim(''.$folder->description);
                $styles = explode(',', trim($desc));
                for ($i = 0; $i < count($styles); ++$i) {
                    $s = explode(' ', trim($styles[$i]));
                    if (2 == count($s)) {
                        // (BLUE * 65536) + (GREEN * 256) + RED
                        $colorRGB = $this->hexToRgb($s[1]);
                        $colorCode = ($colorRGB['b'] * 65536) + ($colorRGB['g'] * 256) + $colorRGB['r'];
                        $this->colors[] = $s[0]."\t".$colorCode;
                    }
                }
            }
            foreach ($folder->Folder as $glf) {
                $this->groundlayouts[] = $this->parseGroundlayout($glf);
            }
        }
    }

    private function parseGroundlayout($folder)
    {
        $layoutName = $folder->name;
        $regions = [];
        $geos = [];
        $labels = [];

        foreach ($folder->Folder as $f) {
            switch ($f->name) {
                case 'Perimiter':
                    foreach ($f->Placemark as $region) {
                        $regions[] = $this->parseRegion($region, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_PERIMITER');
                    }
                    break;

                case 'Runways':
                    foreach ($f->Placemark as $region) {
                        $regions[] = $this->parseRegion($region, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_RUNWAY');
                    }
                    break;

                case 'Taxiways':
                    foreach ($f->Placemark as $region) {
                        $regions[] = $this->parseRegion($region, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_TAXIWAY');
                    }
                    break;

                case 'Aprons':
                    foreach ($f->Placemark as $region) {
                        $regions[] = $this->parseRegion($region, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_APRON');
                    }
                    break;

                case 'Buildings':
                    foreach ($f->Placemark as $geo) {
                        $geos[] = $this->parseGeo($geo, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_BUILDING');
                    }
                    break;

                case 'Markings':
                    foreach ($f->Placemark as $geo) {
                        if(isset($geo->LineString))
                            $geos[] = $this->parseGeo($geo, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_MARKING');
                        if(isset($geo->Polygon))
                            $regions[] = $this->parseRegion($geo, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_MARKING');
                        if(isset($geo->MultiGeometry))
                            $geos[] = $this->parseMultiGeo($geo, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_BUILDING', $f->name);
                    }
                    break;

                case 'Labels':
                    foreach ($f->Placemark as $label) {
                        $labels[] = $this->parseLabel($label, (isset($f->description)) ? $f->description : 'COLOR_AIRPORT_LABELS');
                    }
                    break;

                default:
                    // Do nothing!!!!!!!!!
                    break;
            }
        }

        $layout = [
            'name' => $layoutName,
            'geo' => $geos,
            'regions' => $regions,
            'labels' => $labels,
        ];

        return $layout;
    }

    private function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);
        $length = strlen($hex);
        $rgb['a'] = hexdec(8 == $length ? substr($hex, 0, 2) : 0);
        $rgb['r'] = hexdec(8 == $length ? substr($hex, 2, 2) : (6 == $length ? substr($hex, 0, 2) : 0));
        $rgb['g'] = hexdec(8 == $length ? substr($hex, 4, 2) : (6 == $length ? substr($hex, 2, 2) : 0));
        $rgb['b'] = hexdec(8 == $length ? substr($hex, 6, 2) : (6 == $length ? substr($hex, 4, 2) : 0));

        return $rgb;
    }

    private function DECtoDMS($coord, $isLon = false)
    {
        $val = abs($coord);
        $deg = floor($val);
        $min = floor(($val - $deg) * 60);
        $sec = round(($val - $deg - $min / 60) * 3600 * 1000) / 1000;
        $dirInd = 'N';
        if (!$isLon && $coord < 0) {
            $dirInd = 'S';
        }
        if ($isLon && $coord < 0) {
            $dirInd = 'W';
        }
        if ($isLon && $coord > 0) {
            $dirInd = 'E';
        }

        $str = $dirInd.(($deg < 10 && $deg > -10) ? '00'.$deg : '0'.$deg).'.'.(($min < 10 && $min > -10) ? '0'.$min : $min).'.'.(($sec < 10 && $sec > -10) ? '0'.$sec : $sec);
        if (strlen($str) < 14) {
            while (strlen($str) < 14) {
                if (10 == strlen($str)) {
                    $str .= '.0';
                } else {
                    $str .= '0';
                }
            }
        }

        return $str;
    }

    private function convertCoordinates($cString)
    {
        $cPair = explode(',', $cString);
        $lat = (float) $cPair[1];
        $lon = (float) $cPair[0];
        // Convert now
        $lat = $this->DECtoDMS($lat);
        $lon = $this->DECtoDMS($lon, true);

        return $lat.' '.$lon;
    }

    private function convertCoordinatesSeparated($cString)
    {
        $cPair = explode(',', $cString);
        $lat = (float) $cPair[1];
        $lon = (float) $cPair[0];
        // Convert now
        $lat = $this->DECtoDMS($lat);
        $lon = $this->DECtoDMS($lon, true);

        return $lat.':'.$lon;
    }

    public function build()
    {
        $output = "";

        // General Information
        $output = "[INFO]\nGlG Version 1.1\nVATSim Germany\nZZZZ\nN053.08.35.000\nE009.25.08.000\n60\n38\n-0\n1\n\n";
        // Render The Colors
        $output .= "; -- Define Colors\n";
        for ($i = 0; $i < count($this->colors); ++$i) {
            $output .= '#define '.$this->colors[$i]."\n";
        }

        $geoOutput = "\n[GEO]\n";;
        $regionOutput = "\n[REGIONS]\n";
        $labelOutput = "\n\n\n;================== BELOW CONTENT MUST BE WRITTEN TO .ESE FILE ============================\n\n\n";

        foreach ($this->groundlayouts as $gl) {
            if(isset($gl['geo'])) {
                $geoOutput .= "\n\n\n;===================================================================================\n";
                $geoOutput .= "; ".$gl['name']."\n";
                $geoOutput .= ";===================================================================================\n";
                $geoOutput .= $this->buildGeo($gl['name'], $gl['geo']);
            }

            if(isset($gl['regions'])) {
                $regionOutput .= "\n\n\n;===================================================================================\n";
                $regionOutput .= "; ".$gl['name']."\n";
                $regionOutput .= ";===================================================================================\n";
                $regionOutput .= $this->buildRegion($gl['name'], $gl['regions']);
            }

            if(isset($gl['labels'])) {
                $labelOutput .= "\n\n\n;===================================================================================\n";
                $labelOutput .= "; ".$gl['name']."\n";
                $labelOutput .= ";===================================================================================\n";
                $labelOutput .= $this->buildFreetext($gl['name'], $gl['labels']);
            }
        }

        return $output . $geoOutput . $regionOutput . $labelOutput;

    }


    private function buildGeo($name, $geo) {
        $geoOutput = '';

        foreach ($geo as $g) {
            if(is_array($g) && !isset($g['name']) && !isset($g['coords'])) {
                $geoOutput .= $this->buildGeo($name, $g);
            } else {
                $geoOutput.= ";========== ".$g['name']."\n";
                for($i = 1; $i < count($g['coords']); ++$i) {
                    $geoOutput .= $name."\t\t".$this->convertCoordinates($g['coords'][$i-1])."\t\t".$this->convertCoordinates($g['coords'][$i])."\t".$g['style']."\n";
                }
            }
        }

        return $geoOutput;
    }

    private function buildRegion($name, $region) {
        $regionOutput = '';

        foreach ($region as $r) {
            if(is_array($r) && !isset($r['name']) && !isset($r['coords'])) {
                $regionOutput.= $this->buildRegion($name, $r);
            } else {
                $regionOutput .= 'REGIONNAME '.$name."\n";
                for ($i = 0; $i < count($r['coords']) - 1; ++$i) {
                    if (0 == $i) {
                        $regionOutput .= $r['style']."\t".$this->convertCoordinates($r['coords'][$i])."\n";
                    } else {
                        $regionOutput .= "\t\t".$this->convertCoordinates($r['coords'][$i])."\n";
                    }
                }
            }
        }

        return $regionOutput;
    }

    private function buildFreetext($name, $labels)
    {
        $ftxtOutput = "";

        // Render Labels & Freetext
        for ($i = 0; $i < count($labels); ++$i) {
            // $labelSection .= '; --- '.$gl['name'].' -- '.$gl['labels'][$i]['name']."\n";
            // $labelSection .= $gl['labels'][$i]['name']."\t".$this->convertCoordinates($gl['labels'][$i]['coords'])."\t".$gl['labels'][$i]['style']."\n";
            $ftxtOutput .= '; --- '.$name.' -- '.$labels[$i]['name']."\n";
            $ftxtOutput .= $this->convertCoordinatesSeparated($labels[$i]['coords']).':'.$name.':'.$labels[$i]['name']."\n";
        }

        return $ftxtOutput;
    }
}