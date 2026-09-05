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
            $table->string('admin_warning_category')->nullable()->after('closed_by_admin');
            $table->text('admin_warning_message')->nullable()->after('admin_warning_category');
            $table->timestamp('admin_warned_at')->nullable()->after('admin_warning_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['admin_warning_category', 'admin_warning_message', 'admin_warned_at']);
        });
    }
};
