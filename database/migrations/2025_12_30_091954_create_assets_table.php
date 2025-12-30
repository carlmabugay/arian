<?php

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained();
            $table->foreignId('asset_category_id')->constrained();

            $table->enum('status', AssetStatus::values())->default(AssetStatus::Available->value);
            $table->enum('condition', AssetCondition::values())->default(AssetCondition::New->value);

            $table->string('asset_tag');
            $table->string('serial_number')->nullable();
            $table->string('name');
            $table->text('description')->nullable();

            $table->date('purchased_at')->nullable();
            $table->float('purchase_price')->nullable();

            $table->foreignId('location_id')->nullable()->constrained('locations');
            $table->foreignId('user_id')->nullable()->constrained('users');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
