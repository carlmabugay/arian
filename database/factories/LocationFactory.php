<?php

namespace Database\Factories;

use App\Enums\LocationType;
use App\Models\Company;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::inRandomOrder()->first()->id,
            'name' => $this->faker->locale(),
            'type' => $this->faker->randomElement(LocationType::values()),
        ];
    }
}
