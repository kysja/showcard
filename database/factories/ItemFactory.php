<?php

namespace Database\Factories;

use App\Enums\ItemColorScheme;
use App\Enums\ItemFont;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->safeEmail(),
            'font' => ItemFont::Standard,
            'color_scheme' => ItemColorScheme::WhiteOnBlack,
        ];
    }
}
