<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddressService
{
    public function validateAddress(Request $request)
    {
        return $request->validate([
            'address_line_1' => 'required',
            'address_line_2' => 'nullable',
            'preffered_contact_number' => 'required',
            'postal_code' => 'nullable',
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->where(function ($query) use ($request) {
                    $query->where('state_id', $request->state_id);
                }),
            ],
            'state_id' => [
                'required',
                Rule::exists('states', 'id')
                    ->where(function ($query) use ($request) {
                        $query->where('country_id', $request->country_id);
                    }),
            ],
            'country_id' => [
                'required',
                Rule::exists('countries', 'id'),
            ],
            'is_default' => 'required|boolean',
        ]);
    }
}
