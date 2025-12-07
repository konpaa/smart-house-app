<?php

namespace App\Patterns\Creational\AbstractFactory\Devices;

/**
 * Интерфейс для термостатов
 */
interface ThermostatInterface
{
    /**
     * Установить целевую температуру
     */
    public function setTargetTemperature(float $temperature): void;

    /**
     * Получить целевую температуру
     */
    public function getTargetTemperature(): float;

    /**
     * Получить текущую температуру
     */
    public function getCurrentTemperature(): float;

    /**
     * Включить режим обогрева
     */
    public function setHeatingMode(): void;

    /**
     * Включить режим охлаждения
     */
    public function setCoolingMode(): void;

    /**
     * Получить текущий режим
     */
    public function getMode(): string;

    /**
     * Получить информацию об устройстве
     */
    public function getInfo(): array;
}
