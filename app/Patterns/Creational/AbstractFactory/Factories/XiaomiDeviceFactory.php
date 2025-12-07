<?php

namespace App\Patterns\Creational\AbstractFactory\Factories;

use App\Patterns\Creational\AbstractFactory\Contracts\DeviceFactoryInterface;
use App\Patterns\Creational\AbstractFactory\Devices\LightInterface;
use App\Patterns\Creational\AbstractFactory\Devices\SensorInterface;
use App\Patterns\Creational\AbstractFactory\Devices\ThermostatInterface;
use App\Patterns\Creational\AbstractFactory\Devices\Xiaomi\XiaomiLight;
use App\Patterns\Creational\AbstractFactory\Devices\Xiaomi\XiaomiSensor;
use App\Patterns\Creational\AbstractFactory\Devices\Xiaomi\XiaomiThermostat;

/**
 * Конкретная фабрика для создания устройств Xiaomi
 */
class XiaomiDeviceFactory implements DeviceFactoryInterface
{
    public function createLight(string $name, ?string $roomId = null): LightInterface
    {
        return new XiaomiLight($name, $roomId);
    }

    public function createSensor(string $name, ?string $roomId = null): SensorInterface
    {
        return new XiaomiSensor($name, $roomId);
    }

    public function createThermostat(string $name, ?string $roomId = null): ThermostatInterface
    {
        return new XiaomiThermostat($name, $roomId);
    }
}
