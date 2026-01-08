<?php

namespace Database\Seeders;

use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Helper\ProgressBar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Company
        $this->command->warn(PHP_EOL.'Creating company...');
        $company = $this->withProgressBar(1, fn () => Company::factory()->create());
        $this->command->info('Company created.');

        // Super Admin
        $this->command->warn(PHP_EOL.'Creating admin user...');
        $admin = $this->withProgressBar(1, fn () => User::factory()->create([
            'company_id' => null,
            'role' => UserRole::SuperAdmin->value,
            'name' => 'Admin User',
            'email' => 'admin@carlmabugay.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]));
        $this->command->info('Admin user created.');

        // Users
        $this->command->warn(PHP_EOL.'Creating users...');
        $users = collect();
        $this->withProgressBar(10, function () use ($users) {
            $users->push(User::factory()->create());
        });
        $this->command->info('Users created.');

        // Location
        $this->command->warn(PHP_EOL.'Creating location...');
        $location = $this->withProgressBar(1, fn () => Location::factory()->create());
        $this->command->info('Location created.');

        // Asset Categories
        $this->command->warn(PHP_EOL.'Creating asset categories...');
        $categories = collect();
        $this->withProgressBar(10, function () use ($categories) {
            $categories->push(AssetCategory::factory()->create());
        });
        $this->command->info('Asset categories created.');

        // Assets
        $this->command->warn(PHP_EOL.'Creating assets...');
        $assets = collect();
        $this->withProgressBar(30, function () use ($assets, $company) {

            $assets->push(Asset::factory()->create([
                'company_id' => $company->get('id'),
            ]));
        });
        $this->command->info('Assets created.');

        // Asset Assignments
        $this->command->warn(PHP_EOL.'Creating asset assignments...');
        $assetsToAssign = $assets->random(15);

        $this->withProgressBar($assetsToAssign->count(), function () use ($assetsToAssign, $admin, $users) {

            static $index = 0;
            $asset = $assetsToAssign[$index++];
            $user = $users->random();

            AssetAssignment::factory()->create([
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'assigned_by' => $admin->get('id'),
                'assigned_at' => now()->subDays(rand(5, 60)),
                'returned_at' => null,
            ]);

            $asset->update([
                'status' => AssetStatus::Assigned,
                'user_id' => $user->id,
            ]);
        });

        $assets
            ->diff($assetsToAssign)
            ->each(function (Asset $asset) {
                $asset->update([
                    'status' => AssetStatus::Available,
                    'user_id' => null,
                ]);
            });

        $this->command->info('Asset Assignments created.');
    }

    protected function withProgressBar(int $amount, Closure $createCollectionOfOne): Collection
    {
        $progressBar = new ProgressBar($this->command->getOutput(), $amount);

        $progressBar->start();

        $items = new Collection;

        foreach (range(1, $amount) as $i) {
            $items = $items->merge(
                $createCollectionOfOne()
            );
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->command->getOutput()->writeln('');

        return $items;
    }
}
