# Laravel VK Requester
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/atehnix/laravel-vk-requester/master/LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/atehnix/laravel-vk-requester.svg)](https://packagist.org/packages/atehnix/laravel-vk-requester)
[![Packagist Stats](https://poser.pugx.org/atehnix/laravel-vk-requester/downloads)](https://packagist.org/packages/atehnix/laravel-vk-requester/stats)



Пакет предоставляет удобный способ выполнения запросов к API социальной сети Vk.Сom.

Запросы выполняются в фоновом режиме, используя систему очередей Laravel.
На каждый ответ от API генерируется событие, на которое можно подписаться, обработать/сохранить полученные данные и, при необходимости, добавить новые запросы.

Благодаря такому подходу можно гибко выстраивать цепочки из нескольких взаимосвязанных запросов, добавляя в очередь "дочерние" запросы при получении ответа от "родительского". 

#### Например:
```yaml
    # Получить группы по списку ID
    - groups.getByIds

            # Для каждой группы получить участников
            - groups.getMembers

                    # Каждого участника добавить себе в друзья
                    - friends.add

            # Для каждой группы получить посты
            - wall.get

                    # Для каждого поста получить комментарии
                    - wall.getComments

```

А благодаря автоматическому оборачиванию запросов в ["execute-запросы"](https://vk.com/dev/execute) (по 25 в каждом), выполнение происходит в разы быстрее и понижается вероятность превышения лимитов Vk.Com на кол-во и частоту запросов.

## А можно я без очереди? Мне только спросить..
Конечно можно! В состав пакета входит простой и удобный API-клиент - [atehnix/vk-client](https://github.com/atehnix/vk-client), о возможностях которого можно узнать в его документации.
 
 Впрочем, можно и вовсе установить только его, если вам не нужны очереди запросов :). 
 
 А если нужны, то продолжим:

## Установка
##### Для установки через [Composer](https://getcomposer.org/), выполнить:
```
composer require atehnix/laravel-vk-requester
```

##### Добавить в массив `providers` в файле `config/app.php`:
```php
ATehnix\LaravelVkRequester\VkRequesterServiceProvider::class,
```

##### Выполнить:
```
php artisan vendor:publish --provider="ATehnix\LaravelVkRequester\VkRequesterServiceProvider"
```
##### и
```
php artisan migrate
```

> Внимание! Предполагается, что в вашем Laravel проекте уже настроены [очереди](https://laravel.com/docs/master/queues) и [планировщик задач](https://laravel.com/docs/master/scheduling) (Cron).


## Добавление запроса в очередь
```php
<?php
use ATehnix\LaravelVkRequester\Models\VkRequest;

VkRequest::create([
    'method'     => 'wall.get',
    'parameters' => ['owner_id' => 1],
    'token'      => 'some_token',
]);
```

Раз в минуту (по Cron'у) из таблицы временного хранения все новые запросы переносятся в основную очередь Laravel.

Для уменьшения кол-ва реальных обращений к API, все запросы будут автоматически обернуты в ["execute-запросы"](https://vk.com/dev/execute) по 25 в каждом.


## Подписка на ответы API
В качестве удобного способа подписки на ответы API рекомендуется использовать классы, наследованные от `ATehnix\LaravelVkRequester\Contracts\Subscriber`.

Метод `onSuccess($request, $response)` будет вызываться при успешном выполнении запроса, а метод `onFail($request, $error)` при неудачном.

#### Пример:
```php
<?php

use ATehnix\LaravelVkRequester\Contracts\Subscriber;
use ATehnix\LaravelVkRequester\Models\VkRequest;

class WallGetSubscriber extends Subscriber
{
    /** @var string  Метод API запроса */
    protected $apiMethod = 'wall.get';
    
    /** @var string  Тэг запроса */
    protected $tag = 'default';

    public function onSuccess(VkRequest $request, $response)
    {
        foreach ($response['items'] as $item) {
            // do something...
        }
    }

    public function onFail(VkRequest $request, array $error)
    {
        \Log::alert('Request failed!');
    }
}
```

Все Subscriber'ы необходимо добавить в массив `$subscribe` провайдера `EventServiceProvider`.

```php
<?php

class EventServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        WallGetSubscriber::class,
    ];

    // ...
}
```

## Генерируемые события
Конечно же, слушать ответы API можно и без создания Subscriber'а.
Все запросы генерируют события определенного формата, которые вы можете "слушать" как описано в [разделе "Events"](https://laravel.com/docs/master/events) документации Laravel.

##### В случае успешного выполнения, генерируется событие формата:
```
vk-requester.success: wall.get #default
```

##### В случае ошибки выполнения, генерируется событие формата:
```
vk-requester.fail: wall.get #default
```


## Контекст запросов
По-умолчанию, в имени события присутствует тэг `#default`. При добавлении запроса вы можете в атрибуте `tag` указать любое другое значение тега. Тэг позволяет добавить запросам дополнительный "признак", когда требуется отличать их от других запросов с тем же методом. 

Кроме того, через атрибут запроса `context` можно передать массив с любыми дополнительными данными, которые вы сможете получить при обработке события (`$request->context`).

## Где взять API токен?
Перед тем как начать отправлять запросы, необходимо получить API Token.
Ниже представлен один из способов его получить.

##### Добавьте в `config/services.php`:
```php
<?php return [
    // ...

    'vkontakte' => [
        'client_id'     => env('VKONTAKTE_KEY'),
        'client_secret' => env('VKONTAKTE_SECRET'),
        'redirect'      => env('VKONTAKTE_REDIRECT_URI'),
    ],
];
```

В файле `.env` укажите соответствующие параметры авторизации вашего [VK-приложения](https://vk.com/apps?act=manage).

##### Пример получения токена
```php
<?php

Route::get('vkauth', function (\ATehnix\VkClient\Auth $auth) {
    echo "<a href='{$auth->getUrl()}'> Войти через VK.Com </a><hr>";

    if (Request::exists('code')) {
        echo 'Token: '.$auth->getToken(Request::get('code'));
    }
});
```

> Пример демонстрирует лишь сам принцип получения токена. Как и где вы будете его получать и хранить вы решаете сами.

## License
[MIT](https://raw.github.com/atehnix/laravel-vk-requester/master/LICENSE)
