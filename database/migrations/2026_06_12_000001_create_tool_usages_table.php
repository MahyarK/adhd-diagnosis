<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tool_key', 64);
            $table->string('tool_label');
            $table->string('route_name')->nullable();
            $table->string('locale', 8)->default('en');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'tool_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_usages');
    }
};
