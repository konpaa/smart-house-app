<?php

namespace App\Patterns\Creational\AbstractFactory\Factories;

use App\Patterns\Creational\AbstractFactory\Contracts\DeviceFactoryInterface;
use App\Patterns\Creational\AbstractFactory\Devices\LightInterface;
use App\Patterns\Creational\AbstractFactory\Devices\SensorInterface;
use App\Patterns\Creational\AbstractFactory\Devices\ThermostatInterface;
use App\Patterns\Creational\AbstractFactory\Devices\PhilipsHue\PhilipsHueLight;
use App\Patterns\Creational\AbstractFactory\Devices\PhilipsHue\PhilipsHueSensor;
use App\Patterns\Creational\AbstractFactory\Devices\PhilipsHue\PhilipsHueThermostat;

/**
 * Конкретная фабрика для создания устройств Philips Hue
 */
class PhilipsHueDeviceFactory implements DeviceFactoryInterface
{
    public function createLight(string $name, ?string $roomId = null): LightInterface
    {
        return new PhilipsHueLight($name, $roomId);
    }

    public function createSensor(string $name, ?string $roomId = null): SensorInterface
    {
        return new PhilipsHueSensor($name, $roomId);
    }

    public function createThermostat(string $name, ?string $roomId = null): ThermostatInterface
    {
        return new PhilipsHueThermostat($name, $roomId);
    }
}
