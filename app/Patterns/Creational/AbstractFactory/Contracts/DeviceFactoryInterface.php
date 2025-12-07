<?php

namespace App\Patterns\Creational\AbstractFactory\Contracts;

use App\Patterns\Creational\AbstractFactory\Devices\LightInterface;
use App\Patterns\Creational\AbstractFactory\Devices\SensorInterface;
use App\Patterns\Creational\AbstractFactory\Devices\ThermostatInterface;

/**
 * Абстрактная фабрика для создания устройств умного дома
 *
 * Определяет интерфейс для создания семейства связанных устройств
 * без указания их конкретных классов
 */
interface DeviceFactoryInterface
{
    /**
     * Создать устройство освещения
     */
    public function createLight(string $name, ?string $roomId = null): LightInterface;

    /**
     * Создать датчик
     */
    public function createSensor(string $name, ?string $roomId = null): SensorInterface;

    /**
     * Создать термостат
     */
    public function createThermostat(string $name, ?string $roomId = null): ThermostatInterface;
}
