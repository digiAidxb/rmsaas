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
            $table->string('db_username')->nullable();
            $table->text('db_password')->nullable(); // encrypted
            $table->string('db_host')->default('127.0.0.1');
            $table->integer('db_port')->default(3306);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['db_username', 'db_password', 'db_host', 'db_port']);
        });
    }
};
