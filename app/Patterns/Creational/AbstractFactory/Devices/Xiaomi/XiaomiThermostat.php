<?php

namespace App\Patterns\Creational\AbstractFactory\Devices\Xiaomi;

use App\Patterns\Creational\AbstractFactory\Devices\AbstractThermostat;

/**
 * Конкретная реализация термостата Xiaomi
 */
class XiaomiThermostat extends AbstractThermostat
{
    protected function getBrand(): string
    {
        return 'Xiaomi';
    }

    protected function performSetTargetTemperature(float $temperature): void
    {
        // Логика установки температуры через API Xiaomi
    }

    protected function performGetCurrentTemperature(): float
    {
        // Получение текущей температуры через API Xiaomi
        return $this->currentTemperature;
    }

    protected function performSetHeatingMode(): void
    {
        // Логика переключения режима через API Xiaomi
    }

    protected function performSetCoolingMode(): void
    {
        // Логика переключения режима через API Xiaomi
    }
}
