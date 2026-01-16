<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $locale = \App\Models\Locale::inRandomOrder()->first();
        $locale_id = $locale?->id ?? 1;
        $locale_code = $locale?->code ?? 'en';
        $faker = match ($locale_code) {
            'fr' => \Faker\Factory::create('fr_FR'),
            'es' => \Faker\Factory::create('es_ES'),
            'en' => \Faker\Factory::create('en_US'),
            default => \Faker\Factory::create('en_US'),
        };
        return [
            'key' => $faker->unique()->lexify('key_??????'),
            'content' => $faker->sentence(6),
            'locale_id' => $locale_id,
        ];
    }
}
