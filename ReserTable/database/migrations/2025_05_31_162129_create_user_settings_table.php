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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('dark_mode')->default(false);
            $table->string('language')->default('es');
            $table->string('theme_color')->default('blue');
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('sound_enabled')->default(true);
            $table->string('sidebar_style')->default('expanded'); // expanded, collapsed
            $table->json('dashboard_widgets')->nullable(); // Para personalizar el dashboard
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
