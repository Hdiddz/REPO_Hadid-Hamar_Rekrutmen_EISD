<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        foreach (User::all() as $user) {
            if (! $user->username) {
                $base = Str::slug($user->name, '_') ?: 'user';
                $candidate = strtolower($base);
                $i = 1;
                while (User::where('username', $candidate)->where('id', '!=', $user->id)->exists()) {
                    $candidate = strtolower($base).'_'.$i++;
                }
                $user->update(['username' => $candidate]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
