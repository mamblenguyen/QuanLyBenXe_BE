<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use App\Models\Province;
use App\Http\Resources\System\Province\ProvinceResource;


class ProvincesController extends Controller
{
    public function getAll()
{
    try {
        $provinces = Province::all();

        if ($provinces->isEmpty()) {
            return ResponseService::customResponse(null, 'No provinces found.', 404);
        }

        return ResponseService::customResponse(ProvinceResource::collection($provinces), 'Provinces retrieved successfully.', 200);
    } catch (\Exception $e) {
        return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
    }
}

}
