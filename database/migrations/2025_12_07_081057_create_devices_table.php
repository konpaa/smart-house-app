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
        Schema::create('devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // light, sensor, thermostat, camera, etc.
            $table->string('status')->default('off'); // on, off, active, inactive
            $table->string('mac_address')->unique()->nullable();
            $table->string('ip_address')->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->string('firmware_version')->nullable();
            $table->json('settings')->nullable(); // JSON настройки устройства
            $table->decimal('power_consumption', 8, 2)->nullable(); // потребление в ваттах
            $table->integer('battery_level')->nullable(); // уровень батареи в %
            $table->string('location')->nullable(); // местоположение в комнате
            $table->string('icon')->nullable(); // иконка устройства
            $table->integer('order')->default(0); // порядок сортировки
            $table->boolean('is_active')->default(true);
            $table->uuid('room_id')->nullable();
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('room_id');
            $table->index('user_id');
            $table->index('is_online');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
