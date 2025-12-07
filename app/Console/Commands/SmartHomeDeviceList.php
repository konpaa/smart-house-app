<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SmartHomeDeviceList extends Command
{
    protected $signature = 'smart-home:device:list 
                            {--room= : ID комнаты для фильтрации}';

    protected $description = 'Список всех устройств текущего пользователя';

    public function handle()
    {
        $user = SmartHomeUserLogin::getCurrentUser();

        if (!$user) {
            $this->error('❌ Сначала войдите за пользователя: php artisan smart-home:user:login');
            return Command::FAILURE;
        }

        $query = $user->devices()->with('room')->orderBy('name');

        $roomId = $this->option('room');
        if ($roomId) {
            $query->where('room_id', $roomId);
        }

        $devices = $query->get();

        if ($devices->isEmpty()) {
            $this->warn('У вас пока нет устройств. Создайте устройство: php artisan smart-home:device:create');
            return Command::SUCCESS;
        }

        $this->info("=== Устройства пользователя: {$user->name} ===");

        $tableData = $devices->map(function ($device) {
            return [
                'id' => $device->id,
                'name' => $device->name,
                'type' => $device->type->value,
                'status' => $device->status->value,
                'brand' => $device->settings['brand'] ?? '-',
                'room' => $device->room?->name ?? '-',
                'online' => $device->is_online ? '✅' : '❌',
            ];
        })->toArray();

        $this->table(
            ['ID', 'Название', 'Тип', 'Статус', 'Бренд', 'Комната', 'Онлайн'],
            $tableData
        );

        return Command::SUCCESS;
    }
}
