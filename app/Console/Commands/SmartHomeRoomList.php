<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SmartHomeRoomList extends Command
{
    protected $signature = 'smart-home:room:list';

    protected $description = 'Список всех комнат текущего пользователя';

    public function handle()
    {
        $user = SmartHomeUserLogin::getCurrentUser();

        if (!$user) {
            $this->error('❌ Сначала войдите за пользователя: php artisan smart-home:user:login');
            return Command::FAILURE;
        }

        $rooms = $user->rooms()->orderBy('name')->get();

        if ($rooms->isEmpty()) {
            $this->warn('У вас пока нет комнат. Создайте комнату: php artisan smart-home:room:create');
            return Command::SUCCESS;
        }

        $this->info("=== Комнаты пользователя: {$user->name} ===");

        $tableData = $rooms->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'icon' => $room->icon?->value ?? '-',
                'floor' => $room->floor ?? '-',
                'area' => $room->area ? "{$room->area} м²" : '-',
                'devices' => $room->devices()->count(),
            ];
        })->toArray();

        $this->table(
            ['ID', 'Название', 'Иконка', 'Этаж', 'Площадь', 'Устройств'],
            $tableData
        );

        return Command::SUCCESS;
    }
}
