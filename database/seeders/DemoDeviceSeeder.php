<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::query()
            ->whereIn('email', [
                'customer1@gmail.com',
                'customer2@gmail.com',
                'customer3@gmail.com',
                'customer4@gmail.com',
            ])
            ->get()
            ->keyBy('email');

        $devices = [
            'customer1@gmail.com' => ['device_id' => 'ios-customer-1', 'device_type' => 'phone', 'device_name' => 'iPhone 15', 'device_os' => 'iOS', 'device_os_version' => '18.0'],
            'customer2@gmail.com' => ['device_id' => 'android-customer-2', 'device_type' => 'phone', 'device_name' => 'Galaxy S24', 'device_os' => 'Android', 'device_os_version' => '15'],
            'customer3@gmail.com' => ['device_id' => 'ipad-customer-3', 'device_type' => 'tablet', 'device_name' => 'iPad Air', 'device_os' => 'iPadOS', 'device_os_version' => '18.0'],
            'customer4@gmail.com' => ['device_id' => 'web-customer-4', 'device_type' => 'web', 'device_name' => 'Chrome Desktop', 'device_os' => 'Windows', 'device_os_version' => '11'],
        ];

        foreach ($devices as $email => $deviceData) {
            $customer = $customers->get($email);
            $device = Device::query()->updateOrCreate(
                ['device_id' => $deviceData['device_id']],
                [
                    'fingerprint' => sha1($deviceData['device_id']),
                    'app_version' => '1.0.0',
                    'device_os' => $deviceData['device_os'],
                    'device_os_version' => $deviceData['device_os_version'],
                    'device_type' => $deviceData['device_type'],
                    'device_name' => $deviceData['device_name'],
                    'device_manufacturer' => $deviceData['device_os'] === 'Android' ? 'Samsung' : 'Apple',
                    'is_mobile' => $deviceData['device_type'] !== 'web',
                    'device_token' => 'token-'.$deviceData['device_id'],
                    'last_ip_address' => '127.0.0.1',
                    'last_activity_at' => now()->subMinutes(15),
                ],
            );

            DeviceUser::query()->updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'device_id' => $device->id,
                ],
                [],
            );
        }
    }
}
