<?php

namespace App\Http\Controllers\Authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Navigation\Chart;
use Illuminate\Support\Facades\Auth;

class AuthorizationController extends Controller
{

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Generate Accesstoken to download protected charts
	 *
	 * @param  Request $request [description]
	 * @param  Chart   $chart   [description]
	 * @return [type]           [description]
	 */
	public function authorizeChartAccess(Request $request, Chart $chart)
	{
        if(!$request->ajax()) abort(403);


		if ($chart->public_available) {
            return response()->json([
                'access' => true,
                'token' => false
            ]);
        }
        /**
         * If it is an internal, non-public chart
         * we need to have an authenticated and completely registered account.
         */
        if(!Auth::check() || !$this->account->setup_completed) {
            return response()->json([
                'access' => false,
            ]);
        }


        if(!$chart->getIsGitlabAttribute()) {
            //nonpublic chart but not on gitlab server
            $encodedSecretKey = config('paseto.paseto_key');
            if(empty($encodedSecretKey)) {
                return response()->json([
                    'access' => false,
                ]);
            }
            $secretKey = \ParagonIE\Paseto\Keys\AsymmetricSecretKey::fromEncodedString($encodedSecretKey);
            $token = \ParagonIE\Paseto\Builder::getPublic($secretKey, new \ParagonIE\Paseto\Protocol\Version2())
                ->setExpiration(\Carbon\Carbon::now()->addSeconds(90))
                ->setIssuer('vatsim-germany.org')
                ->setAudience('nav.vatsim-germany.org')
                ->setSubject('VATSIM Germany chart download authorization')
                ->setIssuedAt(\Carbon\Carbon::now())
                ->setNotBefore(\Carbon\Carbon::now())
                ->set('files', [basename($chart->href)]);

            return response()->json([
                'access' => true,
                'token' => $token->toString(),
            ]);

        }
        //nonpublic chart on gitlab server

        // there is no access
        return response()->json([
            'access' => false,
        ]);


	}

}
