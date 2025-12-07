<?php

namespace App\Patterns\Creational\AbstractFactory\Devices;

/**
 * Абстрактный базовый класс для датчиков
 */
abstract class AbstractSensor implements SensorInterface
{
    protected float $value = 0.0;
    protected float $threshold = 0.0;

    public function __construct(
        protected readonly string $name,
        protected readonly ?string $roomId = null,
        protected readonly string $sensorType = 'temperature'
    ) {
    }

    public function getValue(): float
    {
        return $this->performGetValue();
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function setThreshold(float $threshold): void
    {
        $this->threshold = $threshold;
    }

    public function isThresholdExceeded(): bool
    {
        return $this->getValue() > $this->threshold;
    }

    public function getInfo(): array
    {
        return [
            'name' => $this->name,
            'brand' => $this->getBrand(),
            'type' => $this->getType(),
            'sensor_type' => $this->sensorType,
            'room_id' => $this->roomId,
            'value' => $this->getValue(),
            'threshold' => $this->threshold,
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
        return 'sensor';
    }

    /**
     * Выполнить получение значения (специфичная для бренда логика)
     */
    abstract protected function performGetValue(): float;
}
