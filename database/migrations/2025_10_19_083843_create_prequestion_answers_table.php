<?php

use App\Models\Appointment\Appointment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prequestion_answers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(DB::raw('(UUID())'));
            $table->foreignIdFor(Appointment::class);
            $table->unsignedBigInteger('question_id');
            $table->text('answer');
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('prequestions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prequestion_answers');
    }
};
