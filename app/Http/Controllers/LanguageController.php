<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LanguageController extends Controller
{
    /**
     * Handle GET: api/supported-locales
     *
     * @param Request $request
     * @return mixed
     */
    function supportedLocales(Request $request)
    {
        try {
            $file = "common.json";
            $dir = base_path("resources/lang/");
            $locales = array_diff(scandir($dir), array('..', '.'));
            $supportedLocales = [];
            foreach ($locales as $locale) {
                if (file_exists(base_path("resources/lang/$locale/$file"))) {
                    $supportedLocales[] = $locale;
                }
            }
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }

        return $this->sendResponse($supportedLocales, 'Supported Locales retrieved successfully');
    }

    /**
     * Handle GET: api/translations
     *
     * @param Request $request
     * @return mixed
     */
    function translations(Request $request)
    {
        try {
            $this->validate($request, [
                'locale' => 'required|string:10',
            ]);
            $file = "common.json";
            $locale = $request->get('locale', 'en');
            $content = file_get_contents(base_path("resources/lang/$locale/$file"));
            $translation = json_decode($content, true);
        } catch (ValidationException|Exception $exception) {
            return $this->sendError($exception->getMessage());
        }

        return $this->sendResponse($translation, 'Translation retrieved successfully');
    }
}
