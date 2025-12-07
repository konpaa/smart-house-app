<?php

namespace App\Patterns\Creational\AbstractFactory\Devices\Xiaomi;

use App\Patterns\Creational\AbstractFactory\Devices\AbstractLight;

/**
 * Конкретная реализация светильника Xiaomi
 */
class XiaomiLight extends AbstractLight
{
    protected function getBrand(): string
    {
        return 'Xiaomi';
    }

    protected function performTurnOn(): void
    {
        // Логика включения через API Xiaomi
    }

    protected function performTurnOff(): void
    {
        // Логика выключения через API Xiaomi
    }

    protected function performSetBrightness(int $brightness): void
    {
        // Логика установки яркости через API Xiaomi
    }

    protected function performSetColor(int $red, int $green, int $blue): void
    {
        // Логика установки цвета через API Xiaomi
    }
}
