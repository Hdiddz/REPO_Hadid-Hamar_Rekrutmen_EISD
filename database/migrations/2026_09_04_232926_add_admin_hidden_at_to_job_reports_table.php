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
        Schema::table('job_reports', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
            $table->timestamp('admin_hidden_at')->nullable()->after('reporter_hidden_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_reports', function (Blueprint $table) {
            $table->dropColumn('admin_hidden_at');
        });
    }
};
