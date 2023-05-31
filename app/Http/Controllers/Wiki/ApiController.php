<?php

namespace App\Http\Controllers\Wiki;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\Navigation\Aerodrome;

class ApiController extends BaseController
{

    /**
     * Get the general information of an aerodrome identified by it's ICAO code
     *
     * @param  Request $request [description]
     * @param  [type]  $icao    [description]
     * @return [type]           [description]
     */
	public function aerodrome(Request $request, $icao)
	{

		$aerodrome = Aerodrome::icao($icao)->firstOrFail();

		//dd($aerodrome->toArray());

		$result = ArrayToXml::convert(
			$aerodrome->toArray(),
			[
				'rootElementName' => 'data',
			],
			true,
			'UTF-8'
		);

		return response(
			$result,
			200,
			[
				'Content-Type' => 'application/xml',
			]
		);
	}

	/**
	 * Get runway information regarding an aerodrome indentified by it's ICAO
	 *
	 * This function will deliver a paired runway information.
	 * E.g. a runway that has setup an opposite one in the backend will be delivered not as two, but as one runway-pair
	 *
	 * @param  Request $request [description]
	 * @param  [type]  $icao    [description]
	 * @return [type]           [description]
	 */
	public function runways(Request $request, $icao)
	{
		$aerodrome = Aerodrome::icao($icao)->firstOrFail();
		$aerodrome->loadMissing('runways.opposite');

		$runways = $aerodrome->runways;

		$rwyPairs = [
		];

		foreach ($runways as $runway) {
			if(!array_key_exists('__custom:runway:'.$runway->id, $rwyPairs) && ($runway->opposite != null && !array_key_exists('__custom:runway:'.$runway->opposite->id, $rwyPairs)))
			{
				$rwyPairs['__custom:runway:'.$runway->id] = [
					'ident' => $runway->ident,
					'heading' => $runway->heading,
					'dimensions' => $runway->length.'m x '.$runway->width.'m',
					'surface' => $runway->surfaceTypeString,
					'opposite_ident' => $runway->opposite->ident,
					'opposite_heading' => $runway->opposite->heading,
				];
			} elseif (!array_key_exists('__custom:runway:'.$runway->id, $rwyPairs) && $runway->opposite == null) {
				$rwyPairs['__custom:runway:'.$runway->id] = [
					'ident' => $runway->ident,
					'heading' => $runway->heading,
					'dimensions' => $runway->length.'m x '.$runway->width.'m',
					'surface' => $runway->surfaceTypeString
				];
			} elseif ($runway->opposite != null && array_key_exists('__custom:runway:'.$runway->opposite->id, $rwyPairs)) {
				$rwyPairs['__custom:runway:'.$runway->opposite->id]['opposite_ident'] = $runway->ident;
				$rwyPairs['__custom:runway:'.$runway->opposite->id]['opposite_heading'] = $runway->heading;
			}
		}

		$result = ArrayToXml::convert(
			$rwyPairs,
			[
				'rootElementName' => 'runways',
			],
			false,
			'UTF-8'
		);

		return response(
			$result,
			200,
			[
				'Content-Type' => 'application/xml',
			]
		);
	}

	public function stations(Request $request, $icao)
	{
		$aerodrome = Aerodrome::icao($icao)->firstOrFail();
		$aerodrome->loadMissing('stations');

		$stations = [];

		foreach ($aerodrome->stations as $station) {
			$stations['__custom:station:'.$station->id] = [
				'name' => $station->name,
				'ident' => $station->ident,
				'frequency' => $station->fixedFrequency,
				'bookable' => $station->bookable ? 1 : 0,
				'atis' => $station->atis ? 1 : 0,
			];
		}

		$result = ArrayToXml::convert(
			$stations,
			[
				'rootElementName' => 'stations',
			],
			false,
			'UTF-8'
		);

		return response(
			$result,
			200,
			[
				'Content-Type' => 'application/xml',
			]
		);
	}

}
