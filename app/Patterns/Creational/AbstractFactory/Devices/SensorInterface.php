<?php

namespace App\Patterns\Creational\AbstractFactory\Devices;

/**
 * Интерфейс для датчиков
 */
interface SensorInterface
{
    /**
     * Получить текущее значение датчика
     */
    public function getValue(): float;

    /**
     * Получить тип датчика (temperature, humidity, motion, etc.)
     */
    public function getSensorType(): string;

    /**
     * Установить порог срабатывания
     */
    public function setThreshold(float $threshold): void;

    /**
     * Проверить, превышен ли порог
     */
    public function isThresholdExceeded(): bool;

    /**
     * Получить информацию об устройстве
     */
    public function getInfo(): array;
}
