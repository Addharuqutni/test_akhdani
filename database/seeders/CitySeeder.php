<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Jakarta', 'latitude' => -6.2000000, 'longitude' => 106.8166660, 'province_name' => 'DKI Jakarta', 'island_name' => 'Jawa', 'is_foreign' => false, 'is_active' => true],
            ['name' => 'Surabaya', 'latitude' => -7.2574720, 'longitude' => 112.7520900, 'province_name' => 'Jawa Timur', 'island_name' => 'Jawa', 'is_foreign' => false, 'is_active' => true],
            ['name' => 'Bandung', 'latitude' => -6.9174640, 'longitude' => 107.6191230, 'province_name' => 'Jawa Barat', 'island_name' => 'Jawa', 'is_foreign' => false, 'is_active' => true],
            ['name' => 'Medan', 'latitude' => 3.5951960, 'longitude' => 98.6722230, 'province_name' => 'Sumatera Utara', 'island_name' => 'Sumatera', 'is_foreign' => false, 'is_active' => true],
            ['name' => 'Makassar', 'latitude' => -5.1476650, 'longitude' => 119.4327320, 'province_name' => 'Sulawesi Selatan', 'island_name' => 'Sulawesi', 'is_foreign' => false, 'is_active' => true],
            ['name' => 'Denpasar', 'latitude' => -8.6704580, 'longitude' => 115.2126310, 'province_name' => 'Bali', 'island_name' => 'Bali', 'is_foreign' => false, 'is_active' => true],
            ['name' => 'Balikpapan', 'latitude' => -1.2653860, 'longitude' => 116.8312000, 'province_name' => 'Kalimantan Timur', 'island_name' => 'Kalimantan', 'is_foreign' => false, 'is_active' => true],
            ['name' => 'Jayapura', 'latitude' => -2.5337100, 'longitude' => 140.7181320, 'province_name' => 'Papua', 'island_name' => 'Papua', 'is_foreign' => false, 'is_active' => true],

            ['name' => 'Tokyo', 'latitude' => 35.6762000, 'longitude' => 139.6503000, 'province_name' => 'Tokyo', 'island_name' => 'Honshu', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Beijing', 'latitude' => 39.9042020, 'longitude' => 116.4073940, 'province_name' => 'Beijing', 'island_name' => 'Mainland China', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Shanghai', 'latitude' => 31.2303910, 'longitude' => 121.4737010, 'province_name' => 'Shanghai', 'island_name' => 'Mainland China', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'New York', 'latitude' => 40.7127760, 'longitude' => -74.0059740, 'province_name' => 'New York', 'island_name' => 'North America', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Los Angeles', 'latitude' => 34.0522350, 'longitude' => -118.2436830, 'province_name' => 'California', 'island_name' => 'North America', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'London', 'latitude' => 51.5073510, 'longitude' => -0.1277580, 'province_name' => 'England', 'island_name' => 'Great Britain', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Paris', 'latitude' => 48.8566130, 'longitude' => 2.3522220, 'province_name' => 'Ile-de-France', 'island_name' => 'Europe', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Berlin', 'latitude' => 52.5200080, 'longitude' => 13.4049540, 'province_name' => 'Berlin', 'island_name' => 'Europe', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Moscow', 'latitude' => 55.7558250, 'longitude' => 37.6172980, 'province_name' => 'Moscow', 'island_name' => 'Europe', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Dubai', 'latitude' => 25.2048490, 'longitude' => 55.2707820, 'province_name' => 'Dubai', 'island_name' => 'Arabian Peninsula', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Riyadh', 'latitude' => 24.7135510, 'longitude' => 46.6752970, 'province_name' => 'Riyadh', 'island_name' => 'Arabian Peninsula', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Mumbai', 'latitude' => 19.0760900, 'longitude' => 72.8774260, 'province_name' => 'Maharashtra', 'island_name' => 'Indian Subcontinent', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Delhi', 'latitude' => 28.7040600, 'longitude' => 77.1024930, 'province_name' => 'Delhi', 'island_name' => 'Indian Subcontinent', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Sydney', 'latitude' => -33.8688200, 'longitude' => 151.2092900, 'province_name' => 'New South Wales', 'island_name' => 'Australia', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Melbourne', 'latitude' => -37.8136290, 'longitude' => 144.9630580, 'province_name' => 'Victoria', 'island_name' => 'Australia', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Sao Paulo', 'latitude' => -23.5505200, 'longitude' => -46.6333080, 'province_name' => 'Sao Paulo', 'island_name' => 'South America', 'is_foreign' => true, 'is_active' => true],
            ['name' => 'Johannesburg', 'latitude' => -26.2041030, 'longitude' => 28.0473040, 'province_name' => 'Gauteng', 'island_name' => 'Africa', 'is_foreign' => true, 'is_active' => true],
        ];

        foreach ($rows as $row) {
            City::query()->updateOrCreate(['name' => $row['name']], $row);
        }
    }
}
