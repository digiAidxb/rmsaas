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
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('staff')->after('password');
            }
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->unique()->after('permissions');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable()->after('hire_date');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('salary');
            }
            if (!Schema::hasColumn('users', 'emergency_contact')) {
                $table->json('emergency_contact')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('emergency_contact');
            }
            if (!Schema::hasColumn('users', 'notes')) {
                $table->text('notes')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('last_login_ip');
            }
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }
            if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            }
        });
        
        // Add indexes if they don't exist (will fail silently if they do)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['role', 'is_active']);
                $table->index('employee_id');
                $table->index('last_login_at');
            });
        } catch (\Exception $e) {
            // Indexes might already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'permissions', 'employee_id', 'phone', 'date_of_birth',
                'hire_date', 'salary', 'department', 'emergency_contact', 'address',
                'notes', 'last_login_at', 'last_login_ip', 'is_active',
                'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at'
            ]);
        });
    }
};
