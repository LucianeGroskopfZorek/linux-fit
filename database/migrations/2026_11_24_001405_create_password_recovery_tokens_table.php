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
        Schema::create('password_recovery_tokens', function (Blueprint $table) {
            $table->string('email', 254);
            $table->string('tipo', 20);
            $table->string('token', 64);
            $table->timestamp('created_at');
            $table->primary(['email', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_recovery_tokens');
    }
};
