<?php

namespace App\Patterns\Creational\AbstractFactory\Devices\PhilipsHue;

use App\Patterns\Creational\AbstractFactory\Devices\AbstractLight;

/**
 * Конкретная реализация светильника Philips Hue
 */
class PhilipsHueLight extends AbstractLight
{
    protected function getBrand(): string
    {
        return 'Philips Hue';
    }

    protected function performTurnOn(): void
    {
        // Логика включения через API Philips Hue Bridge
    }

    protected function performTurnOff(): void
    {
        // Логика выключения через API Philips Hue Bridge
    }

    protected function performSetBrightness(int $brightness): void
    {
        // Логика установки яркости через API Philips Hue Bridge
    }

    protected function performSetColor(int $red, int $green, int $blue): void
    {
        // Логика установки цвета через API Philips Hue Bridge (HSL конвертация)
    }
}
