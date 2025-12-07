<?php

namespace App\Patterns\Creational\AbstractFactory\Devices;

/**
 * Абстрактный базовый класс для термостатов
 */
abstract class AbstractThermostat implements ThermostatInterface
{
    protected float $targetTemperature = 22.0;
    protected float $currentTemperature = 20.0;
    protected string $mode = 'heating';

    public function __construct(
        protected readonly string $name,
        protected readonly ?string $roomId = null
    ) {
    }

    public function setTargetTemperature(float $temperature): void
    {
        $this->targetTemperature = $temperature;
        $this->performSetTargetTemperature($temperature);
    }

    public function getTargetTemperature(): float
    {
        return $this->targetTemperature;
    }

    public function getCurrentTemperature(): float
    {
        return $this->performGetCurrentTemperature();
    }

    public function setHeatingMode(): void
    {
        $this->mode = 'heating';
        $this->performSetHeatingMode();
    }

    public function setCoolingMode(): void
    {
        $this->mode = 'cooling';
        $this->performSetCoolingMode();
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getInfo(): array
    {
        return [
            'name' => $this->name,
            'brand' => $this->getBrand(),
            'type' => $this->getType(),
            'room_id' => $this->roomId,
            'target_temperature' => $this->targetTemperature,
            'current_temperature' => $this->getCurrentTemperature(),
            'mode' => $this->mode,
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
        return 'thermostat';
    }

    /**
     * Выполнить установку целевой температуры (специфичная для бренда логика)
     */
    abstract protected function performSetTargetTemperature(float $temperature): void;

    /**
     * Выполнить получение текущей температуры (специфичная для бренда логика)
     */
    abstract protected function performGetCurrentTemperature(): float;

    /**
     * Выполнить установку режима обогрева (специфичная для бренда логика)
     */
    abstract protected function performSetHeatingMode(): void;

    /**
     * Выполнить установку режима охлаждения (специфичная для бренда логика)
     */
    abstract protected function performSetCoolingMode(): void;
}
