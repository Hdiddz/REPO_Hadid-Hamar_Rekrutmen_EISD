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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('status');
            $table->text('acceptance_notes')->nullable()->after('start_date');
            $table->date('interview_date')->nullable()->after('acceptance_notes');
            $table->string('interview_time')->nullable()->after('interview_date');
            $table->string('interview_type')->nullable()->after('interview_time');
            $table->string('interview_location')->nullable()->after('interview_type');
            $table->text('interview_notes')->nullable()->after('interview_location');
            $table->string('rejection_reason')->nullable()->after('interview_notes');
            $table->text('rejection_notes')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'acceptance_notes',
                'interview_date',
                'interview_time',
                'interview_type',
                'interview_location',
                'interview_notes',
                'rejection_reason',
                'rejection_notes',
            ]);
        });
    }
};
