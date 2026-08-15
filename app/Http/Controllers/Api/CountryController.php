<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;

class CountryController extends Controller
{
    public function countries()
    {
        $countries = Country::get();

        return $this->formatResponse('Countries fetched successfully', $countries);
    }
}
