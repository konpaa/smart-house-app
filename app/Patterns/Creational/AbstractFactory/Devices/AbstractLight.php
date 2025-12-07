<?php

namespace App\Patterns\Creational\AbstractFactory\Devices;

/**
 * Абстрактный базовый класс для светильников
 */
abstract class AbstractLight implements LightInterface
{
    protected bool $isOn = false;
    protected int $brightness = 50;
    protected array $color = ['red' => 255, 'green' => 255, 'blue' => 255];

    public function __construct(
        protected readonly string $name,
        protected readonly ?string $roomId = null
    ) {
    }

    public function turnOn(): void
    {
        $this->isOn = true;
        $this->performTurnOn();
    }

    public function turnOff(): void
    {
        $this->isOn = false;
        $this->performTurnOff();
    }

    public function setBrightness(int $brightness): void
    {
        $this->brightness = max(0, min(100, $brightness));
        $this->performSetBrightness($brightness);
    }

    public function getBrightness(): int
    {
        return $this->brightness;
    }

    public function setColor(int $red, int $green, int $blue): void
    {
        $this->color = [
            'red' => max(0, min(255, $red)),
            'green' => max(0, min(255, $green)),
            'blue' => max(0, min(255, $blue)),
        ];
        $this->performSetColor($red, $green, $blue);
    }

    public function getInfo(): array
    {
        return [
            'name' => $this->name,
            'brand' => $this->getBrand(),
            'type' => $this->getType(),
            'room_id' => $this->roomId,
            'is_on' => $this->isOn,
            'brightness' => $this->brightness,
            'color' => $this->color,
        ];
    }

    /**
     * Получить бренд устройства (реализуется в конкретных классах)
     */
    abstract protected function getBrand(): string;

    /**
     * Получить тип устройства
     */
    protected function getType(): string
    {
        return 'light';
    }

    /**
     * Выполнить включение (специфичная для бренда логика)
     */
    abstract protected function performTurnOn(): void;

    /**
     * Выполнить выключение (специфичная для бренда логика)
     */
    abstract protected function performTurnOff(): void;

    /**
     * Выполнить установку яркости (специфичная для бренда логика)
     */
    abstract protected function performSetBrightness(int $brightness): void;

    /**
     * Выполнить установку цвета (специфичная для бренда логика)
     */
    abstract protected function performSetColor(int $red, int $green, int $blue): void;
}
