<?php

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Notifications\AssetAssignedNotification;

it('assigns an asset and notifies the assigned user', function () {
    Notification::fake();

    $admin = User::factory()->companyAdmin()->create();
    $staff = User::factory()->staff()->create([
        'company_id' => $admin->company_id,
    ]);

    $asset = Asset::factory()->create([
        'company_id' => $admin->company_id,
    ]);

    AssetAssignment::create([
        'asset_id' => $asset->id,
        'user_id' => $staff->id,
        'assigned_by' => $admin->id,
        'assigned_at' => now(),
    ]);

    expect(AssetAssignment::where('asset_id', $asset->id)
        ->where('user_id', $staff->id)
        ->exists())->toBeTrue();

    Notification::assertSentTo(
        $staff,
        AssetAssignedNotification::class
    );

    Notification::assertNotSentTo(
        $admin,
        AssetAssignedNotification::class
    );
});
