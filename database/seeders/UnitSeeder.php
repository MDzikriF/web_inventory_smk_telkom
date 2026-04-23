<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $units = [
            ['name' => 'Unit'],
            ['name' => 'Pcs'],
            ['name' => 'Box'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['name' => $unit['name']],
                $unit
            );
        }
    }
}
