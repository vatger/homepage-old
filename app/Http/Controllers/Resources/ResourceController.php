<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Models\Filebase\Image;

class ResourceController extends Controller
{
    
	function __construct()
	{
		parent::__construct();
	}

	public function image(Request $request, Image $image)
	{
		// $referer = request()->headers->get('referer');
		// if($referer == null || !preg_match("/^https?:\/\/(\w+\.)?".config('APP_URL')."/", $referer)) {
		// 	abort(403);
		// }

		$fullpath = storage_path('app').'/'.$image->path;
		if($image->approved && File::exists($fullpath))
		{
			$file = File::get($fullpath);
			$type = File::mimeType($fullpath);

			$response = Response::make($file, 200);
		    $response->header("Content-Type", $type);
		    return $response;
		}
		else
			abort(403);
	}

}
