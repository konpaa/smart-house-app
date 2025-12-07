<?php

namespace App\Console\Commands;

use App\Enums\DeviceStatus;
use App\Enums\DeviceType;
use App\Models\Device;
use App\Patterns\Creational\AbstractFactory\Contracts\DeviceFactoryInterface;
use App\Patterns\Creational\AbstractFactory\Enums\DeviceBrand;
use App\Patterns\Creational\AbstractFactory\Traits\HasDeviceFactory;
use Illuminate\Console\Command;

class SmartHomeDeviceCreate extends Command
{
    use HasDeviceFactory;

    protected $signature = 'smart-home:device:create 
                            {--brand= : Бренд устройства (xiaomi, philips)}
                            {--type= : Тип устройства (light, sensor, thermostat)}
                            {--name= : Название устройства}
                            {--room= : ID комнаты (необязательно)}';

    protected $description = 'Создать новое устройство через Abstract Factory';

    public function handle()
    {
        $user = SmartHomeUserLogin::getCurrentUser();

        if (!$user) {
            $this->error('❌ Сначала войдите за пользователя: php artisan smart-home:user:login');
            return Command::FAILURE;
        }

        $this->info('=== Создание нового устройства ===');

        // Выбор бренда
        $availableBrands = $this->getAvailableBrands();
        $brand = $this->option('brand') ?? $this->choice(
            'Выберите бренд устройства',
            $availableBrands,
            DeviceBrand::XIAOMI->value
        );

        // Выбор типа устройства (только те, что поддерживаются Abstract Factory)
        $supportedTypes = [
            DeviceType::LIGHT->value,
            DeviceType::SENSOR->value,
            DeviceType::THERMOSTAT->value,
        ];

        $typeValue = $this->option('type') ?? $this->choice(
            'Выберите тип устройства',
            $supportedTypes,
            DeviceType::LIGHT->value
        );

        $type = DeviceType::from($typeValue);

        // Название устройства
        $name = $this->option('name') ?? $this->ask('Название устройства');

        // Выбор комнаты
        $rooms = $user->rooms()->orderBy('name')->get();
        $roomId = null;

        if ($rooms->isNotEmpty()) {
            $roomOptions = ['- Без комнаты'] + $rooms->pluck('name', 'id')->toArray();
            $selectedRoom = $this->option('room') ?? $this->choice(
                'Выберите комнату',
                array_values($roomOptions),
                array_values($roomOptions)[0]
            );

            if ($selectedRoom !== '- Без комнаты') {
                $roomId = array_search($selectedRoom, $roomOptions);
            }
        }

        // Создание устройства через Abstract Factory
        $factory = $this->getFactory($brand);
        [$device, $info] = $this->createDevice($factory, $type, $name, $roomId, $user->id);

        // Загружаем связь для отображения
        $device->load('room');

        $this->info("✅ Устройство создано через Abstract Factory!");
        $this->info("Информация от Abstract Factory:");
        $this->line(json_encode($info, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->table(
            ['Поле', 'Значение'],
            [
                ['ID', $device->id],
                ['Название', $device->name],
                ['Тип', $device->type->value],
                ['Статус', $device->status->value],
                ['Бренд', $info['brand'] ?? '-'],
                ['Комната', $device->room?->name ?? '-'],
            ]
        );

        return Command::SUCCESS;
    }


    private function createDevice(
        DeviceFactoryInterface $factory,
        DeviceType $type,
        string $name,
        ?string $roomId,
        string $userId
    ): array {
        // Создаем устройство через Abstract Factory
        $deviceInterface = match ($type) {
            DeviceType::LIGHT => $factory->createLight($name, $roomId),
            DeviceType::SENSOR => $factory->createSensor($name, $roomId),
            DeviceType::THERMOSTAT => $factory->createThermostat($name, $roomId),
            default => throw new \InvalidArgumentException("Тип устройства {$type->value} не поддерживается Abstract Factory"),
        };

        // Получаем информацию об устройстве
        $info = $deviceInterface->getInfo();

        // Сохраняем в базу данных
        $device = Device::create([
            'name' => $name,
            'type' => $type,
            'status' => DeviceStatus::OFF,
            'room_id' => $roomId,
            'user_id' => $userId,
            'is_active' => true,
        ]);

        // Сохраняем информацию о бренде в настройках
        $device->settings = ['brand' => $info['brand']];
        $device->save();

        return [$device, $info];
    }
}
