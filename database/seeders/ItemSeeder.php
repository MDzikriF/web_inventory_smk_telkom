<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $units = Unit::all();

        if ($categories->isEmpty() || $units->isEmpty()) {
            $this->command->warn('Please run CategorySeeder and UnitSeeder first.');
            return;
        }

        $items = [
            [
                'name' => 'PC All-In-One',
                'type' => 'Core i5',
                'category_name' => 'Alat',
                'unit_name' => 'Unit',
                'sub_kategori' => 'KBM',
                'stock' => 10,
                'min_stock' => 3
            ],
            [
                'name' => 'Laptop',
                'type' => 'Core i7',
                'category_name' => 'Alat',
                'unit_name' => 'Unit',
                'sub_kategori' => 'KBM',
                'stock' => 15,
                'min_stock' => 5
            ],
            [
                'name' => 'Mouse',
                'type' => 'USB Optical',
                'category_name' => 'Alat',
                'unit_name' => 'Unit',
                'sub_kategori' => 'KBM',
                'stock' => 25,
                'min_stock' => 8
            ],
            [
                'name' => 'Keyboard',
                'type' => 'USB Mechanical',
                'category_name' => 'Alat',
                'unit_name' => 'Unit',
                'sub_kategori' => 'KBM',
                'stock' => 20,
                'min_stock' => 6
            ],
            [
                'name' => 'Monitor',
                'type' => '24 inch LED',
                'category_name' => 'Alat',
                'unit_name' => 'Unit',
                'sub_kategori' => 'KBM',
                'stock' => 12,
                'min_stock' => 4
            ],
            [
                'name' => 'Kertas A4',
                'type' => '80gsm',
                'category_name' => 'Bahan',
                'unit_name' => 'Box',
                'sub_kategori' => 'KBM',
                'stock' => 50,
                'min_stock' => 10
            ],
            [
                'name' => 'Tinta Printer',
                'type' => 'Black',
                'category_name' => 'Bahan',
                'unit_name' => 'Box',
                'sub_kategori' => 'KBM',
                'stock' => 30,
                'min_stock' => 8
            ],
            [
                'name' => 'Spidol',
                'type' => 'Permanent',
                'category_name' => 'Bahan',
                'unit_name' => 'Pcs',
                'sub_kategori' => 'KBM',
                'stock' => 100,
                'min_stock' => 20
            ],
            [
                'name' => 'Oscilloscope',
                'type' => 'Digital 100MHz',
                'category_name' => 'Alat',
                'unit_name' => 'Unit',
                'sub_kategori' => 'Khusus',
                'stock' => 5,
                'min_stock' => 2
            ],
            [
                'name' => 'Multimeter',
                'type' => 'Digital',
                'category_name' => 'Alat',
                'unit_name' => 'Unit',
                'sub_kategori' => 'Khusus',
                'stock' => 8,
                'min_stock' => 3
            ]
        ];

        foreach ($items as $index => $itemData) {
            $category = $categories->where('name', $itemData['category_name'])->first();
            $unit = $units->where('name', $itemData['unit_name'])->first();

            if (!$category || !$unit) {
                continue;
            }

            // Generate kode barang otomatis
            $prefix = strtoupper(substr($itemData['category_name'], 0, 3));
            $kodeBarang = $prefix . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            // Photo URL berdasarkan jenis item
            $photoUrls = [
                'PC All-In-One' => 'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=300&h=300&fit=crop',
                'Laptop' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=300&h=300&fit=crop',
                'Mouse' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=300&h=300&fit=crop',
                'Keyboard' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=300&h=300&fit=crop',
                'Monitor' => 'https://images.unsplash.com/photo-1527443226157-a7a772e78347?w=300&h=300&fit=crop',
                'Kertas A4' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=300&h=300&fit=crop',
                'Tinta Printer' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=300&h=300&fit=crop',
                'Spidol' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=300&h=300&fit=crop',
                'Oscilloscope' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop',
                'Multimeter' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&h=300&fit=crop',
            ];

            Item::updateOrCreate(
                ['kode_barang' => $kodeBarang],
                [
                    'name' => $itemData['name'],
                    'type' => $itemData['type'],
                    'category_id' => $category->id,
                    'unit_id' => $unit->id,
                    'sub_kategori' => $itemData['sub_kategori'],
                    'stock' => $itemData['stock'],
                    'min_stock' => $itemData['min_stock'],
                    'photo' => $photoUrls[$itemData['name']] ?? null,
                ]
            );
        }

        $this->command->info('ItemSeeder completed successfully!');
    }
}
