<?php

namespace Database\Seeders;

use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ShippingAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ShippingAddress::query()->delete();
        Schema::enableForeignKeyConstraints();

        $templates = [
            [
                'name' => 'Rumah',
                'phone' => '0812-3456-7890',
                'address_line1' => '123 Sukhumvit Road',
                'address_line2' => 'Khlong Toei',
                'city' => 'Bangkok',
                'state' => 'Bangkok',
                'postal_code' => '10110',
                'country' => 'Thailand',
            ],
            [
                'name' => 'Kantor',
                'phone' => '0813-9876-5432',
                'address_line1' => '55 Sathorn Square',
                'address_line2' => 'Yan Nawa',
                'city' => 'Bangkok',
                'state' => 'Bangkok',
                'postal_code' => '10120',
                'country' => 'Thailand',
            ],
            [
                'name' => 'Alamat Cadangan',
                'phone' => '0812-0000-1111',
                'address_line1' => '88 Nimmanhaemin Rd',
                'address_line2' => null,
                'city' => 'Chiang Mai',
                'state' => 'Chiang Mai',
                'postal_code' => '50200',
                'country' => 'Thailand',
            ],
        ];

        $users = User::all();
        foreach ($users as $user) {
            foreach ($templates as $index => $template) {
                ShippingAddress::create([
                    'user_id' => $user->id,
                    'name' => $template['name'],
                    'phone' => $template['phone'],
                    'address_line1' => $template['address_line1'],
                    'address_line2' => $template['address_line2'],
                    'city' => $template['city'],
                    'state' => $template['state'],
                    'postal_code' => $template['postal_code'],
                    'country' => $template['country'],
                    'is_default' => $index === 0,
                ]);
            }
        }
    }
}

