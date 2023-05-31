<?php

namespace App\Http\Controllers\Administration\Filebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class DownloadController extends Controller
{
    
	/**
     * Function to handle file download of a given sct file content.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function download(Request $request)
    {
    	if(isset($request->filePath) && Storage::exists( urldecode($request->filePath)))
        	return response()->download(storage_path('app').'/'.urldecode($request->filePath))->deleteFileAfterSend(true);
        else 
        	abort(404);
    }

}
