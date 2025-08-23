<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->enum('action_type', ['create', 'update', 'delete', 'login', 'logout', 'view', 'export', 'import']);
            $table->string('module'); // financial, manufacturing, inventory, orders, etc
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->timestamps();
            
            $table->index(['user_id', 'action_type']);
            $table->index(['module', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('risk_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};