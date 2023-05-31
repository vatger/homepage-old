<?php

namespace App\Http\Controllers\Administration\Navigation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use Storage;
use App\Models\Navigation\SCT;

class GroundlayoutController extends Controller
{
    public function __construct()
    {
        parent::__construct();

		$this->middleware(function ($request, $next) {

			if(!$this->account->hasPermission('administration.navigation'))
				abort(403);

			return $next($request);
		});
    }

    public function render(Request $request)
    {
        if ($request->hasFile('gekml')) {
            if (Str::endsWith($request->file('gekml')->getClientOriginalName(), '.kmz')) {
                $zip = new ZipArchive();
                $res = $zip->open($request->file('gekml'));
                if (true === $res) {
                    $zip->extractTo(storage_path('app').'/temp/'.$this->account->id.'/');
                    $zip->close();
                } else {
                    return redirect()->back()->with('notification', ['type' => 'danger', 'title' => 'SCT Generator', 'message' => 'Failed to load Resource from Archive!']);
                }
                $xmlFileRead = file_get_contents(storage_path('app').'/temp/'.$this->account->id.'/doc.kml');
                // Storage::deleteDirectory('temp/'.$this->account->id);
            } elseif (Str::endsWith($request->file('gekml')->getClientOriginalName(), '.kml')) {
                $xmlFileRead = file_get_contents($request->file('gekml'));
            } else {
                return response()->json(
                    [
                        'output' => 'File type not supported! ('.$request->input('gekml').')',
                    ],
                    200
                );
            }

            \App\Jobs\Navigation\ParseGoogleEarthKMZJob::dispatch($this->account, $xmlFileRead);

            return response()->json([
                'output' => $xmlFileRead,
                'status' => 'Parsing started.'
            ]);
        } else {
            return response()->json(
                [
                    'output' => 'No file to handle! ('.$request->input('gekml').')',
                ],
                200
            );
        }
    }

    /**
     * Function to handle file download of a given sct file content.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function download(Request $request)
    {
        return response()->download(storage_path('app').'/temp/'.$this->account->id.'/sector.sct')->deleteFileAfterSend(true);
    }

    /**
     * Get the sample file.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function downloadSample(Request $request)
    {
        return response()->download(
            storage_path('app').'/navigation/glg/Groundlayouts.kmz',
            'Groundlayouts.kmz',
            [
                'Content-Type' => 'application/vnd.google-earth.kmz .kmz',
            ]
        );
    }
}