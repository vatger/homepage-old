<?php

namespace App\Http\Controllers\Translation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /**
     * Combine all langauge files into a javascript readable array and deliver it as window.i18n.
     *
     * @param Request $request  [description]
     * @param [type]  $language [description]
     *
     * @return [type] [description]
     */
    public function getLanguage(Request $request, $language)
    {
        $strings = \Illuminate\Support\Facades\Cache::remember($language.'/lang.js', 60, function () use ($language) {
            $files = glob(resource_path('lang/'.$language.'/*.php'));
            $strings = [];
            foreach ($files as $f) {
                $name = basename($f, '.php');
                $read = require $f;
                $strings[$name] = $read;
            }

            return $strings;
        });

        // Deliver the package
        header('Content-Type: text/javascript');
        echo 'window.i18n = '.json_encode($strings).';';
        exit();
    }
}
