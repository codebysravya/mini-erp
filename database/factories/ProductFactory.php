<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-??')),
            'name' => fake()->unique()->words(3, true),
            'unit_cost' => fake()->randomFloat(2, 5, 500),
            'stock_quantity' => fake()->numberBetween(20, 100),
            'is_active' => true,
        ];
    }

    public function withStock(int $quantity): static
    {
        return $this->state(fn () => ['stock_quantity' => $quantity]);
    }
}