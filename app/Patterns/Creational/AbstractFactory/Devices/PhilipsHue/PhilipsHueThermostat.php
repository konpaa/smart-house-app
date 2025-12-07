<?php

namespace App\Patterns\Creational\AbstractFactory\Devices\PhilipsHue;

use App\Patterns\Creational\AbstractFactory\Devices\AbstractThermostat;

/**
 * Конкретная реализация термостата Philips Hue
 */
class PhilipsHueThermostat extends AbstractThermostat
{
    protected function getBrand(): string
    {
        return 'Philips Hue';
    }

    protected function performSetTargetTemperature(float $temperature): void
    {
        // Логика установки температуры через API Philips Hue Bridge
    }

    protected function performGetCurrentTemperature(): float
    {
        // Получение текущей температуры через API Philips Hue Bridge
        return $this->currentTemperature;
    }

    protected function performSetHeatingMode(): void
    {
        // Логика переключения режима через API Philips Hue Bridge
    }

    protected function performSetCoolingMode(): void
    {
        // Логика переключения режима через API Philips Hue Bridge
    }
}
