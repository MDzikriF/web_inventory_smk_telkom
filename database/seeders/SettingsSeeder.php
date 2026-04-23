<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('auto_clear_chat_enabled', false, 'boolean', 'Enable/disable auto clear chat feature');
        Setting::set('auto_clear_chat_interval', 'daily', 'string', 'Auto clear chat interval (daily, weekly, monthly)');
    }
}
