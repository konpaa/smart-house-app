<?php

namespace App\Console\Commands;

use App\Enums\RoomIcon;
use App\Models\Room;
use Illuminate\Console\Command;

class SmartHomeRoomCreate extends Command
{
    protected $signature = 'smart-home:room:create 
                            {--name= : Название комнаты}
                            {--description= : Описание комнаты}
                            {--icon= : Иконка комнаты}
                            {--floor= : Этаж}
                            {--area= : Площадь в м²}';

    protected $description = 'Создать новую комнату';

    public function handle()
    {
        $user = SmartHomeUserLogin::getCurrentUser();

        if (!$user) {
            $this->error('❌ Сначала войдите за пользователя: php artisan smart-home:user:login');
            return Command::FAILURE;
        }

        $this->info('=== Создание новой комнаты ===');

        $name = $this->option('name') ?? $this->ask('Название комнаты');
        $description = $this->option('description') ?? $this->ask('Описание (необязательно)', null);

        $iconOptions = RoomIcon::values();
        $icon = $this->option('icon') ?? $this->choice(
            'Иконка комнаты',
            $iconOptions,
            $iconOptions[0] ?? null
        );

        $floor = $this->option('floor') ?? $this->ask('Этаж (необязательно)', null);
        $area = $this->option('area') ?? $this->ask('Площадь в м² (необязательно)', null);

        $room = Room::create([
            'name' => $name,
            'description' => $description,
            'icon' => $icon ? RoomIcon::from($icon) : null,
            'floor' => $floor ? (int) $floor : null,
            'area' => $area ? (float) $area : null,
            'user_id' => $user->id,
        ]);

        $this->info("✅ Комната создана!");
        $this->table(
            ['Поле', 'Значение'],
            [
                ['ID', $room->id],
                ['Название', $room->name],
                ['Описание', $room->description ?? '-'],
                ['Иконка', $room->icon?->value ?? '-'],
                ['Этаж', $room->floor ?? '-'],
                ['Площадь', $room->area ? "{$room->area} м²" : '-'],
            ]
        );

        return Command::SUCCESS;
    }
}
