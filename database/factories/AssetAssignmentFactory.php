<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetAssignment>
 */
class AssetAssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => Asset::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'assigned_by' => User::inRandomOrder()->first()->id,

            'assigned_at' => now()->subDays(rand(1, 30)),
            'returned_at' => null,
            'notes' => $this->faker->sentence(),
        ];
    }
}
