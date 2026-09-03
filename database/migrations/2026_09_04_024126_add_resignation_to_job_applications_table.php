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
            $table->date('resignation_date')->nullable()->after('rejection_notes');
            $table->string('resignation_reason')->nullable()->after('resignation_date');
            $table->text('resignation_notes')->nullable()->after('resignation_reason');
            $table->string('resignation_status')->nullable()->after('resignation_notes');
            $table->timestamp('resigned_at')->nullable()->after('resignation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'resignation_date',
                'resignation_reason',
                'resignation_notes',
                'resignation_status',
                'resigned_at',
            ]);
        });
    }
};
