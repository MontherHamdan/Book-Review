<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => null,
            'review' => fake()->paragraph,
            'rating' => fake()->numberBetween(1, 5),
            'created_at' => fake()->dateTimeBetween('-2 years'),
            'updated_at' => function (array $attribute) {
                return fake()->dateTimeBetween($attribute['created_at']);
            },
        ];
    }

    // this methods to make genereted (rating) more diversity 
    // because if we didnt make this method the rating will be average around 3
    // so we generate bad book and good book and average book
    public function good()
    {
        return $this->state(function (array $attribute) {
            return [
                'rating' => fake()->numberBetween(4, 5)
            ];
        });
    }

    public function average()
    {
        return $this->state(function (array $attribute) {
            return [
                'rating' => fake()->numberBetween(2, 5)
            ];
        });
    }

    public function bad()
    {
        return $this->state(function (array $attribute) {
            return [
                'rating' => fake()->numberBetween(1, 3)
            ];
        });
    }
}
