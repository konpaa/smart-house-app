# Умный дом - Изучение GoF паттернов

Проект для изучения паттернов проектирования из книги "Design Patterns: Elements of Reusable Object-Oriented Software" (Gang of Four).

## Порождающие паттерны (Creational Patterns)

### Abstract Factory (Абстрактная фабрика)

#### Теория

**Abstract Factory** — это порождающий паттерн проектирования, который предоставляет интерфейс для создания семейств связанных или зависимых объектов без указания их конкретных классов.

#### Назначение

- Инкапсулирует создание семейства связанных объектов
- Изолирует клиентский код от конкретных классов продуктов
- Обеспечивает согласованность создаваемых объектов
- Упрощает добавление новых семейств продуктов

#### Структура

```
AbstractFactory (DeviceFactoryInterface)
    ├── ConcreteFactory1 (XiaomiDeviceFactory)
    │   ├── createLight() → XiaomiLight
    │   ├── createSensor() → XiaomiSensor
    │   └── createThermostat() → XiaomiThermostat
    │
    └── ConcreteFactory2 (PhilipsHueDeviceFactory)
        ├── createLight() → PhilipsHueLight
        ├── createSensor() → PhilipsHueSensor
        └── createThermostat() → PhilipsHueThermostat

AbstractProduct (LightInterface, SensorInterface, ThermostatInterface)
    └── ConcreteProduct (XiaomiLight, PhilipsHueLight, etc.)
```

#### Участники

1. **AbstractFactory (DeviceFactoryInterface)** — объявляет интерфейс для операций создания абстрактных продуктов
2. **ConcreteFactory (XiaomiDeviceFactory, PhilipsHueDeviceFactory)** — реализует операции создания конкретных продуктов
3. **AbstractProduct (LightInterface, SensorInterface, ThermostatInterface)** — объявляет интерфейс для типа объекта продукта
4. **ConcreteProduct (XiaomiLight, PhilipsHueLight, etc.)** — определяет объект продукта, создаваемый соответствующей конкретной фабрикой
5. **Client** — использует только интерфейсы, объявленные в AbstractFactory и AbstractProduct

#### Преимущества

- ✅ Изолирует конкретные классы от клиента
- ✅ Упрощает замену семейств продуктов
- ✅ Гарантирует согласованность продуктов
- ✅ Соблюдает принцип открытости/закрытости (Open/Closed Principle)

#### Недостатки

- ❌ Сложность добавления новых типов продуктов (требует изменения интерфейса фабрики)
- ❌ Увеличение количества классов в системе

#### Применимость

Используйте Abstract Factory, когда:
- Система не должна зависеть от того, как создаются, компонуются и представляются продукты
- Система должна конфигурироваться одним из множества семейств продуктов
- Нужно предоставить библиотеку продуктов и раскрыть только их интерфейсы, а не реализации

---

## Реализация в проекте

### Структура файлов

```
app/Patterns/Creational/AbstractFactory/
├── Contracts/
│   └── DeviceFactoryInterface.php          # Абстрактная фабрика
├── Enums/
│   └── DeviceBrand.php                     # Enum для брендов устройств
├── Traits/
│   └── HasDeviceFactory.php                # Трейт для работы с фабриками
├── Factories/
│   ├── XiaomiDeviceFactory.php            # Фабрика устройств Xiaomi
│   └── PhilipsHueDeviceFactory.php        # Фабрика устройств Philips Hue
├── Devices/
│   ├── LightInterface.php                 # Интерфейс светильника
│   ├── SensorInterface.php                # Интерфейс датчика
│   ├── ThermostatInterface.php            # Интерфейс термостата
│   ├── AbstractLight.php                  # Абстрактный класс светильника
│   ├── AbstractSensor.php                 # Абстрактный класс датчика
│   ├── AbstractThermostat.php             # Абстрактный класс термостата
│   ├── Xiaomi/
│   │   ├── XiaomiLight.php
│   │   ├── XiaomiSensor.php
│   │   └── XiaomiThermostat.php
│   └── PhilipsHue/
│       ├── PhilipsHueLight.php
│       ├── PhilipsHueSensor.php
│       └── PhilipsHueThermostat.php
```

### Описание реализации

#### 1. Абстрактная фабрика (DeviceFactoryInterface)

Определяет интерфейс для создания семейства устройств:

```php
interface DeviceFactoryInterface
{
    public function createLight(string $name, ?string $roomId = null): LightInterface;
    public function createSensor(string $name, ?string $roomId = null): SensorInterface;
    public function createThermostat(string $name, ?string $roomId = null): ThermostatInterface;
}
```

#### 2. Конкретные фабрики

**XiaomiDeviceFactory** — создает устройства Xiaomi:
- XiaomiLight
- XiaomiSensor
- XiaomiThermostat

**PhilipsHueDeviceFactory** — создает устройства Philips Hue:
- PhilipsHueLight
- PhilipsHueSensor
- PhilipsHueThermostat

#### 3. Интерфейсы продуктов

Каждый тип устройства имеет свой интерфейс:
- `LightInterface` — управление освещением (включение, яркость, цвет)
- `SensorInterface` — работа с датчиками (получение значений, пороги)
- `ThermostatInterface` — управление температурой (установка цели, режимы)

#### 4. Абстрактные базовые классы

Для устранения дублирования кода и хардкода значений используются абстрактные базовые классы:
- `AbstractLight` — общая логика для всех светильников
- `AbstractSensor` — общая логика для всех датчиков
- `AbstractThermostat` — общая логика для всех термостатов

Эти классы:
- Содержат общую функциональность (состояние, базовые методы)
- Определяют абстрактные методы для получения бренда (`getBrand()`) и типа (`getType()`)
- Выделяют специфичную для бренда логику в отдельные методы (Template Method паттерн)

#### 5. Конкретные продукты

Каждый производитель наследует абстрактные классы и реализует только специфичную для бренда логику:
- Бренд определяется через метод `getBrand()` (не хардкодится)
- Тип устройства определяется через метод `getType()` (не хардкодится)
- Специфичная логика работы с API вынесена в отдельные методы (perform*)

#### 6. Enum для брендов (DeviceBrand)

Централизованное управление брендами через enum:
- `DeviceBrand::XIAOMI` — бренд Xiaomi
- `DeviceBrand::PHILIPS_HUE` — бренд Philips Hue
- Метод `displayName()` для получения отображаемого имени
- Устраняет хардкод названий брендов в коде

#### 7. Трейт HasDeviceFactory

Трейт для устранения дублирования кода в командах:
- `getFactory()` — создание фабрики по бренду
- `getBrandName()` — получение названия бренда из фабрики
- `getAvailableBrands()` — список доступных брендов
- Используется во всех командах для работы с устройствами

### Пример использования

```php
use App\Patterns\Creational\AbstractFactory\Factories\XiaomiDeviceFactory;
use App\Patterns\Creational\AbstractFactory\Factories\PhilipsHueDeviceFactory;

// Создаем фабрику для Xiaomi
$xiaomiFactory = new XiaomiDeviceFactory();
$light = $xiaomiFactory->createLight("Свет в гостиной");
$light->turnOn();
$light->setBrightness(75);

// Создаем фабрику для Philips Hue
$philipsFactory = new PhilipsHueDeviceFactory();
$light = $philipsFactory->createLight("Свет в спальне");
$light->turnOn();
$light->setColor(255, 200, 150);

// Клиентский код работает только с интерфейсами,
// не зная о конкретных классах устройств

// Получение информации об устройстве
$info = $light->getInfo();
// [
//     'name' => 'Свет в спальне',
//     'brand' => 'Philips Hue',  // определяется динамически через getBrand()
//     'type' => 'light',          // определяется динамически через getType()
//     'room_id' => null,
//     'is_on' => true,
//     'brightness' => 50,
//     'color' => ['red' => 255, 'green' => 200, 'blue' => 150]
// ]
```

### Преимущества текущей реализации

✅ **Нет хардкода** — бренд и тип определяются динамически через методы и enum  
✅ **DRY принцип** — общая логика вынесена в абстрактные классы и трейты  
✅ **Template Method** — специфичная логика изолирована в отдельных методах  
✅ **Легко расширять** — добавление нового производителя требует только наследования абстрактного класса и добавления в enum  
✅ **Полиморфизм** — клиентский код работает через интерфейсы, не зная конкретных классов  
✅ **Полная функциональность** — все методы интерфейсов доступны через команды управления  
✅ **Централизованное управление** — бренды управляются через enum `DeviceBrand`

### Демонстрация паттерна в командах

Все консольные команды демонстрируют правильное использование Abstract Factory:

1. **Создание устройств** (`smart-home:device:create`):
   - Создает фабрику нужного бренда
   - Использует фабрику для создания устройства через интерфейс
   - Получает информацию об устройстве через интерфейс `getInfo()`

2. **Взаимодействие с устройствами** (`smart-home:device:control`):
   - Восстанавливает устройство из БД
   - Создает объект через Abstract Factory на основе сохраненного бренда
   - Работает с устройством через интерфейсы (`LightInterface`, `SensorInterface`, `ThermostatInterface`)
   - Использует `instanceof` для определения типа интерфейса (полиморфизм)
   - Поддерживает все методы интерфейсов:
     - **Светильники**: `on`, `off`, `brightness`, `color`, `info`
     - **Датчики**: `value`, `sensor-type`, `threshold`, `check-threshold`, `info`
     - **Термостаты**: `current-temp`, `target-temp`, `set-temp`, `heating`, `cooling`, `mode`, `info`

3. **Демонстрация** (`smart-home:demo`):
   - Показывает полный цикл работы с паттерном
   - Демонстрирует создание устройств разных брендов
   - Показывает взаимодействие через интерфейсы для каждого типа устройства

### Запуск примера

#### Интерактивная демонстрация

```bash
docker compose exec laravel.test php artisan smart-home:demo
```

Эта команда проведет вас через полный цикл:
1. Создание/выбор пользователя
2. Создание комнаты
3. Демонстрация создания устройств через Abstract Factory для разных брендов

#### Консольные команды для работы с системой

**Управление пользователями:**
```bash
# Создать нового пользователя
docker compose exec laravel.test php artisan smart-home:user:login --create

# Войти за пользователя
docker compose exec laravel.test php artisan smart-home:user:login --email=user@example.com
```

**Управление комнатами:**
```bash
# Создать комнату
docker compose exec laravel.test php artisan smart-home:room:create

# Список комнат
docker compose exec laravel.test php artisan smart-home:room:list
```

**Управление устройствами:**
```bash
# Создать устройство через Abstract Factory
docker compose exec laravel.test php artisan smart-home:device:create

# Список устройств
docker compose exec laravel.test php artisan smart-home:device:list

# Управление устройством (интерактивный выбор действия)
docker compose exec laravel.test php artisan smart-home:device:control {device_id}

# Управление с указанием действия:
# Для светильников:
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=on
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=brightness
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=color

# Для датчиков:
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=value
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=check-threshold

# Для термостатов:
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=current-temp
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=set-temp
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=mode
```

#### Полный пример работы

1. **Создайте пользователя:**
```bash
docker compose exec laravel.test php artisan smart-home:user:login --create
```

2. **Создайте комнату:**
```bash
docker compose exec laravel.test php artisan smart-home:room:create
```

3. **Создайте устройства через Abstract Factory:**
```bash
# Создать светильник Xiaomi
docker compose exec laravel.test php artisan smart-home:device:create --brand=xiaomi --type=light --name="Свет в гостиной"

# Создать термостат Philips Hue
docker compose exec laravel.test php artisan smart-home:device:create --brand=philips --type=thermostat --name="Термостат"
```

4. **Просмотрите список устройств:**
```bash
docker compose exec laravel.test php artisan smart-home:device:list
```

5. **Управляйте устройством:**
```bash
# Получить ID устройства из списка, затем:
docker compose exec laravel.test php artisan smart-home:device:control {device_id}

# Или с указанием действия:
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=on
docker compose exec laravel.test php artisan smart-home:device:control {device_id} --action=brightness
```

### Ключевые моменты реализации

#### Полиморфизм через интерфейсы

В командах используется полиморфизм для работы с устройствами:

```php
// Создаем устройство через Abstract Factory
$deviceInterface = $factory->createLight("Свет", $roomId);

// Работаем через интерфейс, не зная конкретный класс
if ($deviceInterface instanceof LightInterface) {
    $deviceInterface->turnOn();  // Работает для XiaomiLight и PhilipsHueLight
    $deviceInterface->setBrightness(75);
}
```

#### Изоляция клиентского кода

Клиентский код (команды) не знает о конкретных классах:
- Не импортирует `XiaomiLight` или `PhilipsHueLight`
- Работает только с интерфейсами (`LightInterface`, `SensorInterface`, `ThermostatInterface`)
- Использует фабрики для создания объектов

#### Восстановление устройств из БД

При управлении устройством (`smart-home:device:control`):
1. Читаем бренд из настроек устройства в БД
2. Создаем соответствующую фабрику
3. Воссоздаем объект устройства через Abstract Factory
4. Работаем с ним через интерфейсы

Это демонстрирует, как Abstract Factory позволяет работать с объектами, созданными в разное время и в разных местах.

### Связь с другими паттернами

- **Factory Method** — Abstract Factory часто реализуется с помощью Factory Method
- **Template Method** — используется в абстрактных классах (`AbstractLight`, `AbstractSensor`, `AbstractThermostat`)
- **Polymorphism** — активно используется для работы с устройствами через интерфейсы
- **Singleton** — конкретные фабрики могут быть реализованы как Singleton (в текущей реализации создаются каждый раз)

---

## Модели данных

Проект использует следующие модели:

- **User** — пользователи системы
- **Room** — комнаты в доме
- **Device** — устройства умного дома

Все модели используют UUID в качестве первичного ключа.

### Поля моделей

#### Room (комната)
- `id` (UUID), `name`, `description`, `icon` (enum), `floor`, `area`, `color`, `is_active`, `order`, `temperature`, `user_id`

#### Device (устройство)
- `id` (UUID), `name`, `type` (enum), `status` (enum), `mac_address`, `ip_address`, `is_online`, `last_seen_at`, `firmware_version`, `settings` (JSON), `power_consumption`, `battery_level`, `location`, `icon`, `order`, `is_active`, `room_id`, `user_id`

## Технические детали

- **PHP 8.2+** — используется для работы с enum
- **Laravel 12** — фреймворк
- **Docker** — контейнеризация
- **MySQL** — база данных
- **UUID** — все первичные ключи используют UUID вместо автоинкремента
