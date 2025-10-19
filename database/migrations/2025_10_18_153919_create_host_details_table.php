<?php

use App\Models\ServiceType;
use App\Models\User;
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
        Schema::create('host_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('status')->default('active');
            $table->string('username', 100);
            $table->foreignIdFor(ServiceType::class);
            $table->string('profile_photo')->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('meet_location');
            $table->string('meet_timezone');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_auto_approve')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('host_details');
    }
};
