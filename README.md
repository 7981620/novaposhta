# Agent-A NovaPoshta

Импорт справочников Новой Почты (регионы, города, отделения), обновление справочника отделений из консоли. Компонент
Livewire для выбора отделения (область, город, отделение).

## Установка

#### Требования

```
Laravel 8
PHP 8.x
```

1. Добавить через composer:

```bash
composer require agenta/agentanovaposhta
```

2. Публикация файлов библиотеки daaner/novaposhta:

```bash
php artisan vendor:publish --provider="Daaner\NovaPoshta\NovaPoshtaServiceProvider"
```

3. Публикация файла конфигурации <i>(config/agentanovaposhta.php)</i>:

```bash
php artisan vendor:publish --tag=config
```

4. Добавить в файл .env

```dotenv
#API-кей "Новой почты" (см. в личном кабинете)
NP_API_KEY=
#кол-во объектов на странице API (по опыту - максимум 100, иначе периодически таймаут)
AGENTA_NP_CHUNK_SIZE=100 
```

<i>В конфиге можно настроить какие типы отделений импортировать в базу данных:</i>

```php
'import_warehouse_type' => [$NORMAL, $SHOP, $CARGO, $POSTOMAT, $POSTOMAT_PB],
```

<i>и какие типы отделений доступны для выбора пользователем:</i>

```php
'allowed_warehouse_type' => [$NORMAL, $SHOP, $CARGO, $POSTOMAT, $POSTOMAT_PB],
```

5. Запуск миграции:

```bash
php artisan migrate
```

6. Запустить первичный импорт данных (может занимать 10-30 минут и более):

```bash
#импорт областей и населенных пунктов
php artisan np:import_cities
#импорт отделений
php artisan np:update_warehouses
```

## Использование

#### Обновление данных

"Новая Почта" рекомендует обновлять справочник отделений один раз в сутки. Для этого следует запускать команду консоли
(данные будут только добавляться, без удаления уже существующих — если отделение не работающее, то модели будет
установлен <i>active = false</i>):

```bash
php artisan np:update_warehouses
```

Также можно обновлять справочник населенных пунктов и областей (без удаления, только создание и обновление):

```bash
php artisan np:import_cities
```

#### Компонент Livewire

1. Опубликуйте шаблон компонента и оформите его нужными стилями:

```bash
php artisan vendor:publish --tag="views-agentanovaposhta"
```

2. Добавьте в свой blade-шаблон

```injectablephp
       ...
        @livewireStyles
    </head>
<body>

    @livewire('novaposhta-select-warehouse')
    ...
    @livewireScripts
    
</body>
```

компоненту можно передавать параметр 'cargo' => true для отображения только тех городов и отделений, которые являются
грузовыми (для грузов свыше 30 кг), а также предыдущие выбранные значения области, города и отделения для отображения в случае перезагрузки страницы

```injectablephp
@livewire('novaposhta-select-warehouse', [
    'cargo' => true,
    'selectedRegion' => old('np_region_id'),
    'selectedCity' => old('np_city_id'),
    'selectedWarehouse' => old('np_warehouse_id'),
])
```

## Автор

- [Oleksii Berkovskii](https://github.com/agenta)
