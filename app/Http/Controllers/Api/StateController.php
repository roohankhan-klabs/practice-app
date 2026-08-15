<?php

namespace App\Http\Controllers\Api;

use App\Models\State;

class StateController extends Controller
{
    public function states(int $countryId)
    {
        $states = State::where('country_id', $countryId)->get();

        return $this->formatResponse('States fetched successfully', $states);
    }
}
