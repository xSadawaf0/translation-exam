<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
        ];
        foreach ($locales as $locale) {
            \App\Models\Locale::firstOrCreate(['code' => $locale['code']], $locale);
        }

        \App\Models\Translation::factory(100000)->create();
    }
}
