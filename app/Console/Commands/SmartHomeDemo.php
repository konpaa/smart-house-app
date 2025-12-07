<?php

namespace App\Console\Commands;

use App\Enums\DeviceStatus;
use App\Enums\DeviceType;
use App\Models\Device;
use App\Models\Room;
use App\Models\User;
use App\Patterns\Creational\AbstractFactory\Contracts\DeviceFactoryInterface;
use App\Patterns\Creational\AbstractFactory\Devices\LightInterface;
use App\Patterns\Creational\AbstractFactory\Devices\SensorInterface;
use App\Patterns\Creational\AbstractFactory\Devices\ThermostatInterface;
use App\Patterns\Creational\AbstractFactory\Enums\DeviceBrand;
use App\Patterns\Creational\AbstractFactory\Factories\PhilipsHueDeviceFactory;
use App\Patterns\Creational\AbstractFactory\Factories\XiaomiDeviceFactory;
use App\Patterns\Creational\AbstractFactory\Traits\HasDeviceFactory;
use Illuminate\Console\Command;

class SmartHomeDemo extends Command
{
    use HasDeviceFactory;

    protected $signature = 'smart-home:demo';

    protected $description = 'Интерактивная демонстрация паттерна Abstract Factory';

    public function handle()
    {
        $this->info('╔═══════════════════════════════════════════════════════════╗');
        $this->info('║   Демонстрация паттерна Abstract Factory для умного дома  ║');
        $this->info('╚═══════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Создаем или получаем пользователя
        $user = $this->setupUser();
        if (!$user) {
            return Command::FAILURE;
        }

        // Создаем комнату
        $room = $this->setupRoom($user);
        if (!$room) {
            return Command::FAILURE;
        }

        // Демонстрируем создание устройств через Abstract Factory
        $this->demonstrateAbstractFactory($user, $room);

        return Command::SUCCESS;
    }

    private function setupUser(): ?User
    {
        $this->info('=== Шаг 1: Настройка пользователя ===');

        $email = $this->ask('Введите email пользователя', 'demo@example.com');
        $user = User::where('email', $email)->first();

        if (!$user) {
            if ($this->confirm('Пользователь не найден. Создать нового?', true)) {
                $name = $this->ask('Имя пользователя', 'Demo User');
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt('password'),
                ]);
                $this->info("✅ Пользователь создан: {$user->name}");
            } else {
                return null;
            }
        } else {
            $this->info("✅ Пользователь найден: {$user->name}");
        }

        SmartHomeUserLogin::saveCurrentUser($user->id);
        $this->newLine();

        return $user;
    }

    private function setupRoom(User $user): ?Room
    {
        $this->info('=== Шаг 2: Настройка комнаты ===');

        $room = $user->rooms()->first();

        if (!$room) {
            if ($this->confirm('Комната не найдена. Создать новую?', true)) {
                $name = $this->ask('Название комнаты', 'Гостиная');
                $room = Room::create([
                    'name' => $name,
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);
                $this->info("✅ Комната создана: {$room->name}");
            } else {
                return null;
            }
        } else {
            $this->info("✅ Используется комната: {$room->name}");
        }

        $this->newLine();
        return $room;
    }

    private function demonstrateAbstractFactory(User $user, Room $room): void
    {
        $this->info('=== Шаг 3: Демонстрация Abstract Factory ===');
        $this->newLine();

        $brands = [
            DeviceBrand::XIAOMI->value => new XiaomiDeviceFactory(),
            DeviceBrand::PHILIPS_HUE->value => new PhilipsHueDeviceFactory(),
        ];

        foreach ($brands as $brandName => $factory) {
            $this->info("📦 Создание устройств бренда: " . strtoupper($brandName));
            $this->line('─────────────────────────────────────────────────────');

            // Создаем устройства разных типов
            $this->createAndDemonstrateDevice($factory, DeviceType::LIGHT->value, 'Светильник', $user, $room);
            $this->createAndDemonstrateDevice($factory, DeviceType::SENSOR->value, 'Датчик температуры', $user, $room);
            $this->createAndDemonstrateDevice($factory, DeviceType::THERMOSTAT->value, 'Термостат', $user, $room);

            $this->newLine();
        }

        $this->info('✅ Демонстрация завершена!');
        $this->info('Используйте команды для управления:');
        $this->line('  - php artisan smart-home:device:list');
        $this->line('  - php artisan smart-home:device:control {device_id}');
    }

    private function createAndDemonstrateDevice(
        DeviceFactoryInterface $factory,
        string $type,
        string $name,
        User $user,
        Room $room
    ): void {
        // Конвертируем строку в enum
        $deviceType = DeviceType::from($type);

        // ============================================
        // КЛЮЧЕВОЙ МОМЕНТ: Создание через Abstract Factory
        // Клиентский код не знает о конкретных классах!
        // Работаем только с интерфейсами
        // ============================================
        $deviceInterface = match ($deviceType) {
            DeviceType::LIGHT => $factory->createLight("{$name} ({$this->getBrandName($factory)})", $room->id),
            DeviceType::SENSOR => $factory->createSensor("{$name} ({$this->getBrandName($factory)})", $room->id),
            DeviceType::THERMOSTAT => $factory->createThermostat("{$name} ({$this->getBrandName($factory)})", $room->id),
            default => null,
        };

        if (!$deviceInterface) {
            return;
        }

        // Получаем информацию об устройстве через интерфейс
        $info = $deviceInterface->getInfo();
        $this->line("  ✓ Создано: {$info['brand']} {$info['type']} - {$info['name']}");

        // ============================================
        // ВЗАИМОДЕЙСТВИЕ С УСТРОЙСТВОМ ЧЕРЕЗ ИНТЕРФЕЙСЫ
        // Демонстрируем полиморфизм - работа с разными типами
        // через их специфичные интерфейсы
        // Используем instanceof для проверки типа интерфейса
        // ============================================
        $this->demonstrateDeviceInteraction($deviceInterface);

        // Сохраняем в базу для дальнейшего использования
        $device = Device::create([
            'name' => $info['name'],
            'type' => $deviceType,
            'status' => DeviceStatus::OFF,
            'room_id' => $room->id,
            'user_id' => $user->id,
            'is_active' => true,
            'settings' => ['brand' => $info['brand']],
        ]);

        $this->line("    💾 Сохранено в БД (ID: {$device->id})");
    }

    /**
     * Демонстрация взаимодействия с устройством через интерфейсы
     * Показывает, как клиентский код работает с продуктами Abstract Factory
     * Используем instanceof для определения типа интерфейса
     */
    private function demonstrateDeviceInteraction($deviceInterface): void
    {
        // Проверяем тип через instanceof - полиморфизм в действии!
        // Не знаем конкретный класс, только интерфейс
        if ($deviceInterface instanceof LightInterface) {
            $this->interactWithLight($deviceInterface);
        } elseif ($deviceInterface instanceof SensorInterface) {
            $this->interactWithSensor($deviceInterface);
        } elseif ($deviceInterface instanceof ThermostatInterface) {
            $this->interactWithThermostat($deviceInterface);
        }
    }

    /**
     * Взаимодействие со светильником через LightInterface
     * Не знаем, это XiaomiLight или PhilipsHueLight - работаем через интерфейс!
     */
    private function interactWithLight(LightInterface $light): void
    {
        // Используем методы интерфейса LightInterface
        $light->turnOn();
        $this->line("    🔆 Включен через LightInterface::turnOn()");

        $light->setBrightness(75);
        $brightness = $light->getBrightness();
        $this->line("    💡 Яркость установлена: {$brightness}% через LightInterface::setBrightness()");

        $light->setColor(255, 200, 150);
        $this->line("    🎨 Цвет установлен: RGB(255, 200, 150) через LightInterface::setColor()");

        // Получаем финальное состояние через интерфейс
        $info = $light->getInfo();
        $this->line("    📊 Состояние: " . ($info['is_on'] ? 'ВКЛ' : 'ВЫКЛ') . ", яркость: {$info['brightness']}%");
    }

    /**
     * Взаимодействие с датчиком через SensorInterface
     */
    private function interactWithSensor(SensorInterface $sensor): void
    {
        // Используем методы интерфейса SensorInterface
        $sensorType = $sensor->getSensorType();
        $this->line("    📡 Тип датчика: {$sensorType} (через SensorInterface::getSensorType())");

        $sensor->setThreshold(25.0);
        $this->line("    ⚙️  Порог установлен: 25.0 (через SensorInterface::setThreshold())");

        $value = $sensor->getValue();
        $this->line("    📊 Текущее значение: {$value} (через SensorInterface::getValue())");

        $exceeded = $sensor->isThresholdExceeded();
        $this->line("    ⚠️  Порог " . ($exceeded ? 'превышен' : 'не превышен') . " (через SensorInterface::isThresholdExceeded())");
    }

    /**
     * Взаимодействие с термостатом через ThermostatInterface
     */
    private function interactWithThermostat(ThermostatInterface $thermostat): void
    {
        // Используем методы интерфейса ThermostatInterface
        $currentTemp = $thermostat->getCurrentTemperature();
        $this->line("    🌡️  Текущая температура: {$currentTemp}°C (через ThermostatInterface::getCurrentTemperature())");

        $thermostat->setTargetTemperature(22.5);
        $targetTemp = $thermostat->getTargetTemperature();
        $this->line("    🎯 Целевая температура: {$targetTemp}°C (через ThermostatInterface::setTargetTemperature())");

        $thermostat->setHeatingMode();
        $mode = $thermostat->getMode();
        $this->line("    🔥 Режим: {$mode} (через ThermostatInterface::setHeatingMode())");

        // Получаем полную информацию через интерфейс
        $info = $thermostat->getInfo();
        $this->line("    📊 Состояние: цель={$info['target_temperature']}°C, текущая={$info['current_temperature']}°C, режим={$info['mode']}");
    }

}
