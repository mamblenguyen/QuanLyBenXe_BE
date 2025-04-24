<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use App\Models\Province;
use App\Http\Resources\System\Province\ProvinceResource;
use App\Models\District;
use App\Models\Ward;

class ProvincesController extends Controller
{
    public function getAllProvince()
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


    public function getAllDistrict($id)
    {
        try {
            $district = District::getDistrictByProvinceId($id);
            
            if ($district->isEmpty()) {
                return ResponseService::customResponse(null, 'No district found.', 404);
            }

            return ResponseService::customResponse(ProvinceResource::collection($district), 'district retrieved successfully.', 200);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }


    public function getAllWard($id)
    {
        try {
            $ward = Ward::getWardByDistrictId($id);
            
            if ($ward->isEmpty()) {
                return ResponseService::customResponse(null, 'No ward found.', 404);
            }

            return ResponseService::customResponse(ProvinceResource::collection($ward), 'ward retrieved successfully.', 200);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
