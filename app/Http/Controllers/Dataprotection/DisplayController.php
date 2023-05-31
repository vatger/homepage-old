<?php

namespace App\Http\Controllers\Dataprotection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DisplayController extends Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function gdpr()
	{
		$gdpr = \Illuminate\Support\Facades\Storage::get('static/gdpr.html');
		return $this->viewMake('frontend.dataprotection.gdpr')
			->with('gdpr', $gdpr);
	}

	public function imprint()
	{
        $imprint = \Illuminate\Support\Facades\Storage::get('static/imprint.html');
        return $this->viewMake('frontend.dataprotection.imprint')
            ->with('imprint', $imprint);
	}

}
