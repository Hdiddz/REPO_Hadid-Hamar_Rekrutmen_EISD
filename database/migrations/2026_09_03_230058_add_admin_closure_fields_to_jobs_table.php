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
        Schema::table('jobs', function (Blueprint $table) {
            $table->text('closed_reason')->nullable()->after('status');
            $table->timestamp('closed_until')->nullable()->after('closed_reason');
            $table->boolean('closed_by_admin')->default(false)->after('closed_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['closed_reason', 'closed_until', 'closed_by_admin']);
        });
    }
};
