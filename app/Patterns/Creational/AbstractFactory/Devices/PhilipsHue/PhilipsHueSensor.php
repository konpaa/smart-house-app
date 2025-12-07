<?php

namespace App\Patterns\Creational\AbstractFactory\Devices\PhilipsHue;

use App\Patterns\Creational\AbstractFactory\Devices\AbstractSensor;

/**
 * Конкретная реализация датчика Philips Hue
 */
class PhilipsHueSensor extends AbstractSensor
{
    protected function getBrand(): string
    {
        return 'Philips Hue';
    }

    protected function performGetValue(): float
    {
        // Получение значения через API Philips Hue Bridge
        return $this->value;
    }
}
