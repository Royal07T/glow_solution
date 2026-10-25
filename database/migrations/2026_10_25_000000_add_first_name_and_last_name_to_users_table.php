<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
        });

        \Illuminate\Support\Facades\DB::table('users')
            ->whereNull('first_name')
            ->whereNotNull('name')
            ->get(['id', 'name'])
            ->each(function ($user) {
                $parts = preg_split('/\s+/', trim($user->name), 2);
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'first_name' => $parts[0] ?? null,
                        'last_name' => $parts[1] ?? null,
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
