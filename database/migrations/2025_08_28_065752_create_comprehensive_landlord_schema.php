<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Landlord Database Schema
     * This creates all admin/landlord specific tables for managing the SaaS platform
     */
    public function up(): void
    {
        // Countries table for international support
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 2)->unique(); // ISO 2-letter country code
            $table->string('currency_code', 3)->default('USD');
            $table->decimal('tax_rate', 5, 2)->default(0.00); // Tax percentage
            $table->json('tax_settings')->nullable(); // Tax rules and exemptions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['is_active', 'code']);
        });

        // Subscription plans for the SaaS platform
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2);
            $table->json('features'); // List of features included
            $table->json('limits'); // Usage limits (storage, users, etc.)
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });

        // Admin users for managing the SaaS platform
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('admin'); // admin, super_admin
            $table->json('permissions')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index(['is_active', 'role']);
            $table->index('email');
        });

        // Enhanced tenants table (already exists but will be modified)
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('subscription_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'suspended', 'cancelled'])->default('pending');
            $table->json('service_types')->nullable(); // dine-in, takeaway, etc.
            $table->string('business_type')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->text('business_address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->date('trial_ends_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->references('id')->on('admin_users');
            $table->text('rejection_reason')->nullable();
            $table->json('usage_limits')->nullable(); // Storage, users, etc.
            $table->json('usage_current')->nullable(); // Current usage stats
            $table->timestamp('last_activity_at')->nullable();
        });

        // Tenant subscriptions for billing
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->string('stripe_subscription_id')->nullable();
            $table->enum('status', ['active', 'cancelled', 'past_due', 'incomplete', 'incomplete_expired', 'trialing', 'unpaid']);
            $table->enum('billing_cycle', ['monthly', 'yearly']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('AED');
            $table->date('current_period_start');
            $table->date('current_period_end');
            $table->date('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index('stripe_subscription_id');
        });

        // Payment history for all transactions
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3);
            $table->enum('status', ['pending', 'succeeded', 'failed', 'refunded', 'partially_refunded']);
            $table->enum('type', ['subscription', 'one_time', 'refund']);
            $table->text('description');
            $table->json('stripe_metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['invoice_number']);
        });

        // System settings and configurations
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string'); // string, json, boolean, number
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Can be accessed by tenants
            $table->timestamps();
            
            $table->index('key');
        });

        // Activity logs for admin actions
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address');
            $table->text('user_agent');
            $table->timestamps();
            
            $table->index(['admin_user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });

        // Email templates for system notifications
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('subject');
            $table->text('html_content');
            $table->text('text_content');
            $table->json('variables')->nullable(); // Available template variables
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'is_active']);
        });

        // File uploads tracking
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->string('disk')->default('s3');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->enum('type', ['import', 'image', 'document', 'export']);
            $table->enum('status', ['uploading', 'completed', 'failed', 'deleted']);
            $table->timestamps();
            
            $table->index(['tenant_id', 'type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('admin_activity_logs');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('tenant_subscriptions');
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['subscription_plan_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'country_id', 'subscription_plan_id', 'status', 'service_types', 
                'business_type', 'contact_person', 'phone', 'business_address', 
                'city', 'postal_code', 'trial_ends_at', 'approved_at', 
                'approved_by', 'rejection_reason', 'usage_limits', 
                'usage_current', 'last_activity_at'
            ]);
        });
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('countries');
    }
};