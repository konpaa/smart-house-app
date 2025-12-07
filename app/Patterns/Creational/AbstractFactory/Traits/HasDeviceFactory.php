<?php

namespace App\Patterns\Creational\AbstractFactory\Traits;

use App\Patterns\Creational\AbstractFactory\Contracts\DeviceFactoryInterface;
use App\Patterns\Creational\AbstractFactory\Enums\DeviceBrand;
use App\Patterns\Creational\AbstractFactory\Factories\PhilipsHueDeviceFactory;
use App\Patterns\Creational\AbstractFactory\Factories\XiaomiDeviceFactory;

/**
 * Трейт для работы с фабриками устройств
 * Устраняет дублирование кода создания фабрик
 */
trait HasDeviceFactory
{
    /**
     * Получить фабрику по бренду
     */
    protected function getFactory(string $brand): DeviceFactoryInterface
    {
        // Нормализуем бренд (приводим к lowercase)
        $brandNormalized = strtolower($brand);

        return match ($brandNormalized) {
            DeviceBrand::XIAOMI->value => new XiaomiDeviceFactory(),
            DeviceBrand::PHILIPS_HUE->value, 'philips' => new PhilipsHueDeviceFactory(),
            default => throw new \InvalidArgumentException("Неизвестный бренд: {$brand}"),
        };
    }

    /**
     * Получить название бренда из фабрики
     */
    protected function getBrandName(DeviceFactoryInterface $factory): string
    {
        return match (get_class($factory)) {
            XiaomiDeviceFactory::class => DeviceBrand::XIAOMI->displayName(),
            PhilipsHueDeviceFactory::class => DeviceBrand::PHILIPS_HUE->displayName(),
            default => 'Unknown',
        };
    }

    /**
     * Получить список доступных брендов
     */
    protected function getAvailableBrands(): array
    {
        return DeviceBrand::values();
    }

    /**
     * Получить список доступных брендов с отображаемыми именами
     */
    protected function getAvailableBrandsWithNames(): array
    {
        return array_combine(
            DeviceBrand::values(),
            DeviceBrand::displayNames()
        );
    }
}
