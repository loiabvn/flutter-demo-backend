<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Handle GET: api/settings
     *
     * @param Request $request
     * @return JsonResponse
     */
    function settings(Request $request)
    {
        $settings = [
            'language' => 'en',
        ];

        return $this->sendResponse($settings, 'Settings retrieved successfully');
    }
}
