<?php

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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->enum('salary_type', ['hourly', 'daily', 'monthly']);
            $table->decimal('salary_amount', 12, 2);
            $table->unsignedInteger('work_hours_per_day');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['employer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
