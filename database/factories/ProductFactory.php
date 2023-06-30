<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->sentence(3);
        $slug = Str::slug($name);
        return [
            "name" => $name,
            "slug" => $slug,
            "description" => fake()->sentence(),
            "price" => fake()->numberBetween(1, 100)
        ];
    }
}
