<?php

namespace App\Patterns\Creational\AbstractFactory\Devices\Xiaomi;

use App\Patterns\Creational\AbstractFactory\Devices\AbstractSensor;

/**
 * Конкретная реализация датчика Xiaomi
 */
class XiaomiSensor extends AbstractSensor
{
    protected function getBrand(): string
    {
        return 'Xiaomi';
    }

    protected function performGetValue(): float
    {
        // Получение значения через API Xiaomi
        // В реальном приложении здесь был бы запрос к устройству
        return $this->value;
    }
}
