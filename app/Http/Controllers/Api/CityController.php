<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\City;

class CityController extends Controller
{
    public function cities(int $stateId)
    {
        $cities = City::where('state_id', $stateId)->get();

        return $this->formatResponse('Cities fetched successfully', $cities);
    }
}
