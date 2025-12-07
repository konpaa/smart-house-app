<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // bedroom, kitchen, living-room, etc.
            $table->integer('floor')->nullable();
            $table->decimal('area', 8, 2)->nullable(); // площадь в м²
            $table->string('color', 7)->nullable(); // hex цвет для UI
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // порядок сортировки
            $table->decimal('temperature', 5, 2)->nullable(); // текущая температура
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
