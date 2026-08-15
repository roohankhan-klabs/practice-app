<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    private AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses;

        return $this->formatResponse('Addresses fetched successfully', $addresses);
    }
    public function show(Request $request, int $addressId)
    {
        $address = Address::where('user_id', $request->user()->id)->where('id', $addressId)->first();

        if (!$address) {
            return $this->formatError('Address not found', 404);
        }

        return $this->formatResponse('Address fetched successfully', $address);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        $address = Address::create([
            'user_id' => $request->user()->id,
            'address_line_1' => $validated['address_line_1'],
            'address_line_2' => $validated['address_line_2'],
            'preffered_contact_number' => $validated['preffered_contact_number'],
            'postal_code' => $validated['postal_code'],
            'city_id' => $validated['city_id'],
            'state_id' => $validated['state_id'],
            'country_id' => $validated['country_id'],
            'is_default' => $validated['is_default'],
        ]);

        return $this->formatResponse('Address created successfully', $address);
    }
    public function update(Request $request, int $addressId)
    {
        $validated = $this->addressService->validateAddress($request);

        $address = Address::where('user_id', $request->user()->id)->where('id', $addressId)->first();

        if (!$address) {
            return $this->formatError('Address not found', 404);
        }

        $address->update($validated);

        return $this->formatResponse('Address updated successfully', $address);
    }
    public function destroy(Request $request, int $addressId)
    {
        $address = Address::where('user_id', $request->user()->id)->where('id', $addressId)->first();

        if (!$address) {
            return $this->formatError('Address not found', 404);
        }

        $address->delete();

        return $this->formatResponse('Address deleted successfully');
    }
}
