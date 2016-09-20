<?php
/**
 * This file is part of laravel-vk-requester package.
 *
 * @author ATehnix <atehnix@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ATehnix\LaravelVkRequester;

use ATehnix\LaravelVkRequester\Commands\VkRequesterGeneratorCommand;
use ATehnix\VkClient\Auth;
use ATehnix\VkClient\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;

class VkRequesterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../publish/config/' => config_path()], 'config');
        $this->publishes([__DIR__.'/../publish/database/' => database_path('migrations')], 'database');

        if (config('vk-requester.auto_dispatch', true)) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('vk-requester:generate')->everyMinute();
            });
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(Client::class, function () {
            return new Client(config('vk-requester.version', '5.53'));
        });

        $this->app->singleton(Auth::class, function () {
            return new Auth(
                config('services.vkontakte.client_id'),
                config('services.vkontakte.client_secret'),
                config('services.vkontakte.redirect'),
                implode(',', config('vk-requester.scope', []))
            );
        });

        $this->commands([VkRequesterGeneratorCommand::class]);
    }
}
