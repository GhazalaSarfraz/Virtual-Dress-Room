<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            $table->string('site_name')->default('AURA');
            $table->string('tagline')->nullable();

            $table->string('welcome_title')->nullable();
            $table->text('welcome_description')->nullable();

            $table->string('editorial_image')->nullable();

            $table->string('editorial_small_text')->nullable();
            $table->string('editorial_heading')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};