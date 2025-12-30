<?php

namespace Database\Factories;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
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
            'asset_category_id' => AssetCategory::inRandomOrder()->first()->id,
            'status' => $this->faker->randomElement(AssetStatus::values()),
            'condition' => $this->faker->randomElement(AssetCondition::values()),
            'asset_tag' => $this->faker->unique()->word,
            'serial_number' => $this->faker->randomAscii,
            'name' => $this->faker->word,
            'description' => $this->faker->text,
            'purchased_at' => $this->faker->date(),
            'purchase_price' => $this->faker->randomFloat(2, 10),
            'location_id' => Location::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
