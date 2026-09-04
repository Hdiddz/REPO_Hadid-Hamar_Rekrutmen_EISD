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
            $table->timestamp('jobseeker_hidden_at')->nullable()->after('rejected_at');
            $table->timestamp('employer_hidden_at')->nullable()->after('jobseeker_hidden_at');
        });

        Schema::table('job_reports', function (Blueprint $table) {
            $table->timestamp('reporter_hidden_at')->nullable()->after('action_taken');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['jobseeker_hidden_at', 'employer_hidden_at']);
        });

        Schema::table('job_reports', function (Blueprint $table) {
            $table->dropColumn('reporter_hidden_at');
        });
    }
};
