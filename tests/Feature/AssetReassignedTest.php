<?php

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use App\Notifications\AssetReassignedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('notifies old and new users when an asset is reassigned', function () {
    Notification::fake();

    $company = Company::factory()->create();
    $location = Location::factory()->create([
        'company_id' => $company->id,
    ]);
    $admin = User::factory()->companyAdmin()->create([
        'company_id' => $company->id,
    ]);

    $oldUser = User::factory()->staff()->create([
        'company_id' => $company->id,
    ]);

    $newUser = User::factory()->staff()->create([
        'company_id' => $company->id,
    ]);

    $category = AssetCategory::factory()->create();

    $asset = Asset::factory()->create([
        'company_id' => $admin->company_id,
        'asset_category_id' => $category->id,
        'location_id' => $location->id,
        'user_id' => $oldUser->id,
    ]);

    // Initial assignment
    $assignment = AssetAssignment::create([
        'asset_id' => $asset->id,
        'user_id' => $oldUser->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    Notification::clearResolvedInstances();

    // Reassignment
    $assignment->update([
        'user_id' => $newUser->id,
    ]);

    Notification::assertSentTo(
        $oldUser,
        AssetReassignedNotification::class
    );

    Notification::assertSentTo(
        $assignment->user,
        AssetReassignedNotification::class
    );
});
