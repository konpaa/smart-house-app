<?php

namespace App\Patterns\Creational\AbstractFactory\Devices;

/**
 * Интерфейс для устройств освещения
 */
interface LightInterface
{
    /**
     * Включить свет
     */
    public function turnOn(): void;

    /**
     * Выключить свет
     */
    public function turnOff(): void;

    /**
     * Установить яркость (0-100)
     */
    public function setBrightness(int $brightness): void;

    /**
     * Получить текущую яркость
     */
    public function getBrightness(): int;

    /**
     * Установить цвет (RGB)
     */
    public function setColor(int $red, int $green, int $blue): void;

    /**
     * Получить информацию об устройстве
     */
    public function getInfo(): array;
}
