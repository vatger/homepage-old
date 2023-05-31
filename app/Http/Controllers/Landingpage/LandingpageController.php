<?php

namespace App\Http\Controllers\Landingpage;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use View;

class LandingpageController extends Controller
{

	function __construct()
	{
		parent::__construct();
	}

    /**
     * The landing page
     * @param  Request $request [description]
     * @return \View           [description]
     */
	public function index(Request $request)
	{
        $view = View::make('frontend.landingpage.index');
        $view->with('_account', $this->account);
        $view->with('partners', Partner::all());
        return $view;
	}

    /**
     * The partners page
     * @param  Request $request [description]
     * @return \View           [description]
     */
    public function partners(Request $request)
    {
        $view = View::make('frontend.landingpage.partners');
        $view->with('_account', $this->account);
        $view->with('partners', Partner::all());
        return $view;
    }


}
