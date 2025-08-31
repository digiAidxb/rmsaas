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
        Schema::table('users', function (Blueprint $table) {
            $table->string('preferred_language', 5)->default('en')->after('email');
            $table->string('timezone')->default('UTC')->after('preferred_language');
            $table->json('language_preferences')->nullable()->after('timezone');
            
            $table->index('preferred_language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['preferred_language']);
            $table->dropColumn(['preferred_language', 'timezone', 'language_preferences']);
        });
    }
};
