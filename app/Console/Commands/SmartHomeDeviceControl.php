<?php

namespace App\Console\Commands;

use App\Enums\DeviceStatus;
use App\Enums\DeviceType;
use App\Models\Device;
use App\Patterns\Creational\AbstractFactory\Contracts\DeviceFactoryInterface;
use App\Patterns\Creational\AbstractFactory\Devices\LightInterface;
use App\Patterns\Creational\AbstractFactory\Devices\SensorInterface;
use App\Patterns\Creational\AbstractFactory\Devices\ThermostatInterface;
use App\Patterns\Creational\AbstractFactory\Enums\DeviceBrand;
use App\Patterns\Creational\AbstractFactory\Traits\HasDeviceFactory;
use Illuminate\Console\Command;

class SmartHomeDeviceControl extends Command
{
    use HasDeviceFactory;

    protected $signature = 'smart-home:device:control 
                            {device : ID устройства}
                            {--action= : Действие (on, off, brightness, color, temperature)}';

    protected $description = 'Управление устройством через Abstract Factory';

    public function handle()
    {
        $user = SmartHomeUserLogin::getCurrentUser();

        if (!$user) {
            $this->error('❌ Сначала войдите за пользователя: php artisan smart-home:user:login');
            return Command::FAILURE;
        }

        $device = Device::where('id', $this->argument('device'))
            ->where('user_id', $user->id)
            ->first();

        if (!$device) {
            $this->error('❌ Устройство не найдено!');
            return Command::FAILURE;
        }

        $this->info("=== Управление устройством: {$device->name} ===");

        // Получаем бренд из настроек
        $brand = $device->settings['brand'] ?? DeviceBrand::XIAOMI->value;
        $factory = $this->getFactory($brand);

        // Создаем объект устройства через Abstract Factory
        $deviceInterface = $this->createDeviceInterface($factory, $device);

        // Выполняем действие
        $availableActions = $this->getAvailableActions($deviceInterface);
        $action = $this->option('action') ?? $this->choice(
            'Выберите действие',
            $availableActions,
            $availableActions[0] ?? 'info'
        );

        $this->performAction($deviceInterface, $device, $action);

        return Command::SUCCESS;
    }


    private function createDeviceInterface(DeviceFactoryInterface $factory, Device $device): LightInterface|SensorInterface|ThermostatInterface
    {
        return match ($device->type) {
            DeviceType::LIGHT => $factory->createLight($device->name, $device->room_id),
            DeviceType::SENSOR => $factory->createSensor($device->name, $device->room_id),
            DeviceType::THERMOSTAT => $factory->createThermostat($device->name, $device->room_id),
            default => throw new \InvalidArgumentException("Неподдерживаемый тип устройства"),
        };
    }

    private function getAvailableActions($deviceInterface): array
    {
        // Определяем доступные действия на основе типа интерфейса
        if ($deviceInterface instanceof LightInterface) {
            return ['on', 'off', 'brightness', 'color', 'info'];
        } elseif ($deviceInterface instanceof SensorInterface) {
            return ['value', 'sensor-type', 'threshold', 'check-threshold', 'info'];
        } elseif ($deviceInterface instanceof ThermostatInterface) {
            return ['current-temp', 'target-temp', 'set-temp', 'heating', 'cooling', 'mode', 'info'];
        }
        return ['info'];
    }

    private function performAction($deviceInterface, Device $device, string $action): void
    {
        // Используем замыкания для выполнения действий с проверкой типа интерфейса
        $actions = [
            'on' => function () use ($deviceInterface, $device) {
                $this->turnOn($deviceInterface, $device);
            },
            'off' => function () use ($deviceInterface, $device) {
                $this->turnOff($deviceInterface, $device);
            },
            'brightness' => function () use ($deviceInterface, $device) {
                if ($deviceInterface instanceof LightInterface) {
                    $this->setBrightness($deviceInterface, $device);
                } else {
                    $this->error('Действие доступно только для светильников');
                }
            },
            'color' => function () use ($deviceInterface, $device) {
                if ($deviceInterface instanceof LightInterface) {
                    $this->setColor($deviceInterface, $device);
                } else {
                    $this->error('Действие доступно только для светильников');
                }
            },
            'heating' => function () use ($deviceInterface, $device) {
                if ($deviceInterface instanceof ThermostatInterface) {
                    $this->setHeatingMode($deviceInterface, $device);
                } else {
                    $this->error('Действие доступно только для термостатов');
                }
            },
            'cooling' => function () use ($deviceInterface, $device) {
                if ($deviceInterface instanceof ThermostatInterface) {
                    $this->setCoolingMode($deviceInterface, $device);
                } else {
                    $this->error('Действие доступно только для термостатов');
                }
            },
            'value' => function () use ($deviceInterface) {
                if ($deviceInterface instanceof SensorInterface) {
                    $this->getValue($deviceInterface);
                } else {
                    $this->error('Действие доступно только для датчиков');
                }
            },
            'sensor-type' => function () use ($deviceInterface) {
                if ($deviceInterface instanceof SensorInterface) {
                    $this->getSensorType($deviceInterface);
                } else {
                    $this->error('Действие доступно только для датчиков');
                }
            },
            'threshold' => function () use ($deviceInterface) {
                if ($deviceInterface instanceof SensorInterface) {
                    $this->setThreshold($deviceInterface);
                } else {
                    $this->error('Действие доступно только для датчиков');
                }
            },
            'check-threshold' => function () use ($deviceInterface) {
                if ($deviceInterface instanceof SensorInterface) {
                    $this->checkThreshold($deviceInterface);
                } else {
                    $this->error('Действие доступно только для датчиков');
                }
            },
            'current-temp' => function () use ($deviceInterface) {
                if ($deviceInterface instanceof ThermostatInterface) {
                    $this->getCurrentTemperature($deviceInterface);
                } else {
                    $this->error('Действие доступно только для термостатов');
                }
            },
            'target-temp' => function () use ($deviceInterface) {
                if ($deviceInterface instanceof ThermostatInterface) {
                    $this->getTargetTemperature($deviceInterface);
                } else {
                    $this->error('Действие доступно только для термостатов');
                }
            },
            'set-temp' => function () use ($deviceInterface, $device) {
                if ($deviceInterface instanceof ThermostatInterface) {
                    $this->setTemperature($deviceInterface, $device);
                } else {
                    $this->error('Действие доступно только для термостатов');
                }
            },
            'mode' => function () use ($deviceInterface) {
                if ($deviceInterface instanceof ThermostatInterface) {
                    $this->getMode($deviceInterface);
                } else {
                    $this->error('Действие доступно только для термостатов');
                }
            },
            'info' => function () use ($deviceInterface) {
                $this->showInfo($deviceInterface);
            },
        ];

        if (isset($actions[$action])) {
            $actions[$action]();
        } else {
            $this->error("Неизвестное действие: {$action}");
        }
    }

    private function turnOn($deviceInterface, Device $device): void
    {
        if ($deviceInterface instanceof LightInterface) {
            $deviceInterface->turnOn();
            $device->status = DeviceStatus::ON;
            $device->save();
            $this->info("✅ Устройство включено!");
        }
    }

    private function turnOff($deviceInterface, Device $device): void
    {
        if ($deviceInterface instanceof LightInterface) {
            $deviceInterface->turnOff();
            $device->status = DeviceStatus::OFF;
            $device->save();
            $this->info("✅ Устройство выключено!");
        }
    }

    private function setBrightness(LightInterface $deviceInterface, Device $device): void
    {
        $brightness = (int) $this->ask('Яркость (0-100)', 50);
        $deviceInterface->setBrightness($brightness);
        $this->info("✅ Яркость установлена: {$brightness}%");
    }

    private function setColor(LightInterface $deviceInterface, Device $device): void
    {
        $red = (int) $this->ask('Красный (0-255)', 255);
        $green = (int) $this->ask('Зеленый (0-255)', 255);
        $blue = (int) $this->ask('Синий (0-255)', 255);
        $deviceInterface->setColor($red, $green, $blue);
        $this->info("✅ Цвет установлен: RGB({$red}, {$green}, {$blue})");
    }

    private function setTemperature(ThermostatInterface $deviceInterface, Device $device): void
    {
        $temp = (float) $this->ask('Целевая температура', 22.0);
        $deviceInterface->setTargetTemperature($temp);
        $this->info("✅ Целевая температура установлена: {$temp}°C");
    }

    private function setHeatingMode(ThermostatInterface $deviceInterface, Device $device): void
    {
        $deviceInterface->setHeatingMode();
        $this->info("✅ Режим обогрева включен");
    }

    private function setCoolingMode(ThermostatInterface $deviceInterface, Device $device): void
    {
        $deviceInterface->setCoolingMode();
        $this->info("✅ Режим охлаждения включен");
    }

    private function getValue(SensorInterface $deviceInterface): void
    {
        $value = $deviceInterface->getValue();
        $type = $deviceInterface->getSensorType();
        $this->info("📊 Значение датчика ({$type}): {$value}");
    }

    private function setThreshold(SensorInterface $deviceInterface): void
    {
        $threshold = (float) $this->ask('Порог срабатывания', 25.0);
        $deviceInterface->setThreshold($threshold);
        $this->info("✅ Порог установлен: {$threshold}");
    }

    private function checkThreshold(SensorInterface $deviceInterface): void
    {
        $exceeded = $deviceInterface->isThresholdExceeded();
        $value = $deviceInterface->getValue();
        $threshold = $deviceInterface->getInfo()['threshold'] ?? 0;

        $this->info("📊 Текущее значение: {$value}");
        $this->info("⚙️  Порог: {$threshold}");
        $this->info($exceeded ? "⚠️  Порог ПРЕВЫШЕН!" : "✅ Порог не превышен");
    }

    private function getSensorType(SensorInterface $deviceInterface): void
    {
        $type = $deviceInterface->getSensorType();
        $this->info("📡 Тип датчика: {$type}");
    }

    private function getCurrentTemperature(ThermostatInterface $deviceInterface): void
    {
        $temp = $deviceInterface->getCurrentTemperature();
        $this->info("🌡️  Текущая температура: {$temp}°C");
    }

    private function getTargetTemperature(ThermostatInterface $deviceInterface): void
    {
        $temp = $deviceInterface->getTargetTemperature();
        $this->info("🎯 Целевая температура: {$temp}°C");
    }

    private function getMode(ThermostatInterface $deviceInterface): void
    {
        $mode = $deviceInterface->getMode();
        $modeName = match ($mode) {
            'heating' => 'Обогрев',
            'cooling' => 'Охлаждение',
            default => $mode,
        };
        $this->info("🔥 Текущий режим: {$modeName} ({$mode})");
    }

    private function showInfo($deviceInterface): void
    {
        $info = $deviceInterface->getInfo();
        $this->info("📋 Информация об устройстве:");
        $this->line(json_encode($info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
