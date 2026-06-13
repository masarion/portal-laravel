<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('auth_mode', 10)->default('name'); // 'name' or 'code'
            $table->uuid('submit_token')->unique();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->dateTime('deadline');
            $table->text('info_message')->default('');
            $table->text('copy_guide_message')->default('');
            $table->text('confirm_message')->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
