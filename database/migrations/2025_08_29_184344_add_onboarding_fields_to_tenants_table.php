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
        Schema::table('tenants', function (Blueprint $table) {
            $table->json('onboarding_status')->nullable()->after('settings');
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_status');
            $table->boolean('skip_onboarding')->default(false)->after('onboarding_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['onboarding_status', 'onboarding_completed_at', 'skip_onboarding']);
        });
    }
};
