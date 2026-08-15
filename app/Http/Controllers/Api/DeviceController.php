<?php

namespace App\Http\Controllers\Api;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function sync(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:255',
            'device_type' => 'required|in:android,ios,web',
            'device_token' => 'required|string|max:255',
            'fingerprint' => 'nullable|string|max:255',
            'app_version' => 'nullable|string|max:255',
            'device_os' => 'nullable|string|max:255',
            'device_os_version' => 'nullable|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'device_manufacturer' => 'nullable|string|max:255',
            'is_mobile' => 'nullable|boolean',
            'last_ip_address' => 'nullable|ip',
            'last_activity_at' => 'nullable|timestamp',
        ]);

        $device = Device::updateOrCreate(
            [
                'device_id' => $validated['device_id'],
            ],
            [
                'device_type' => $validated['device_type'],
                'device_token' => $validated['device_token'],
                'fingerprint' => $validated['fingerprint'],
                'app_version' => $validated['app_version'],
                'device_os' => $validated['device_os'],
                'device_os_version' => $validated['device_os_version'],
                'device_name' => $validated['device_name'],
                'device_manufacturer' => $validated['device_manufacturer'],
                'is_mobile' => $validated['is_mobile'],
                'last_ip_address' => $validated['last_ip_address'],
                'last_activity_at' => $validated['last_activity_at'],
                'user_id' => $request->user()->id,
            ]
        );

        return $this->formatResponse('Device synced successfully', $device);
    }
}
