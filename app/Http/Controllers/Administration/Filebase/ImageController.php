<?php

namespace App\Http\Controllers\Administration\Filebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Filebase\Image;

class ImageController extends Controller
{

	function __construct()
	{
		parent::__construct();
	}

	public function images(Request $request)
	{
		if(!$this->account->hasPermission('administration.images.manage'))
		{
			if($this->account->hasPermission('administration.images.upload')) {
				return Image::accountId($this->account->id)->orderBy('name', 'ASC')->get();
			}
			abort(403);
		}

		return Image::with('account')->orderBy('name', 'ASC')->get();
	}

	public function myImages(Request $request)
	{
		if($this->account->hasPermission('administration.images.upload')) {
			return Image::accountId($this->account->id)->orderBy('name', 'ASC')->get();
		}
		abort(403);
	}

	public function getImage(Request $request, Image $image)
	{
		if(!$this->account->hasPermission('administration.images.manage'))
			abort(403);

		if(Storage::exists($image->path))
			return base64_encode(Storage::get($image->path));
		else {
			$image->delete();
			return false;
		}
	}

	public function uploadImage(Request $request)
	{
		if(!$this->account->hasPermission('administration.images.upload'))
			abort(403);

		$validated = $request->validate([
			'imgFile' => 'required|file|image|max:5000',
			'imgLicense' => 'required|accepted'
		]);

		$filename = $request->file('imgFile')->getClientOriginalName();
		$fileext = $request->file('imgFile')->extension();

		$now = \Carbon\Carbon::now()->timestamp;

		$filename = $now.'_'.$filename;

		// Store the file
		$path = Storage::putFileAs('images/'.$this->account->id, $request->file('imgFile'), $filename);

		// Insert into database here
		$image = new Image;
		$image->account_id = $this->account->id;
		$image->path = $path;
		$image->name = $filename;
		$image->ext = $fileext;
		$image->approved = true; // For now auto approve images
		$image->save();

		// return a response
		return response()->json([
			'image' => $image
		]);
	}

	public function approveImage(Request $request, Image $image)
	{
		if(!$this->account->hasPermission('administration.images.manage'))
			abort(403);

		$image->approved = true;
		return $image->save();
	}

	public function denyImage(Request $request, Image $image)
	{
		if(!$this->account->hasPermission('administration.images.manage'))
			abort(403);

		$image->approved = false;
		return $image->save();
	}

	public function deleteImage(Request $request, Image $image)
	{
		if(!$this->account->hasPermission('administration.images.manage'))
		{
			if(!$this->account->hasPermission('administration.images.upload') && $image->account_id != $this->account->id)
			{
				abort(403);
			}
		}

		if(Storage::exists($image->path)) {
			Storage::delete($image->path);
		}

		return $image->delete();
	}

}
