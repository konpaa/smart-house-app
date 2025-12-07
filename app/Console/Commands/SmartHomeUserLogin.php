<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class SmartHomeUserLogin extends Command
{
    protected $signature = 'smart-home:user:login 
                            {--email= : Email пользователя}
                            {--create : Создать нового пользователя}';

    protected $description = 'Войти за пользователя или создать нового';

    public function handle()
    {
        if ($this->option('create')) {
            return $this->createUser();
        }

        return $this->loginUser();
    }

    private function createUser(): int
    {
        $this->info('=== Создание нового пользователя ===');

        $name = $this->ask('Имя пользователя');
        $email = $this->ask('Email');
        $password = $this->secret('Пароль');

        if (User::where('email', $email)->exists()) {
            $this->error("Пользователь с email {$email} уже существует!");
            return Command::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("✅ Пользователь создан! ID: {$user->id}");
        $this->info("Имя: {$user->name}");
        $this->info("Email: {$user->email}");

        // Сохраняем ID пользователя в cache
        $this->saveCurrentUser($user->id);

        return Command::SUCCESS;
    }

    private function loginUser(): int
    {
        $email = $this->option('email') ?? $this->ask('Email пользователя');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Пользователь с email {$email} не найден!");
            $this->info("Используйте --create для создания нового пользователя");
            return Command::FAILURE;
        }

        $this->info("✅ Вход выполнен!");
        $this->info("ID: {$user->id}");
        $this->info("Имя: {$user->name}");
        $this->info("Email: {$user->email}");

        $this->saveCurrentUser($user->id);

        return Command::SUCCESS;
    }

    private function saveCurrentUser(string $userId): void
    {
        self::setCurrentUser($userId);
    }

    public static function setCurrentUser(string $userId): void
    {
        Cache::put('smart_home_current_user_id', $userId, now()->addDays(30));
    }

    public static function getCurrentUserId(): ?string
    {
        return Cache::get('smart_home_current_user_id');
    }

    public static function getCurrentUser(): ?User
    {
        $userId = self::getCurrentUserId();
        if (!$userId) {
            return null;
        }
        return User::find($userId);
    }
}
