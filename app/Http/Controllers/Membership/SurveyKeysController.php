<?php

namespace App\Http\Controllers\Membership;

use App\Http\Controllers\Controller;
use App\Models\SurveyKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SurveyKeysController extends Controller
{
    public function myKeys(Request $request)
    {
        if(!$request->ajax()) abort(403);
        $id = Auth::user()->id;
        $keys = SurveyKey::query()->where('account_id', $id)->orderBy('id', 'DESC')->get();

        return response()->json($keys);
    }

}
