<?php
/**
 * This file is part of laravel-vk-requester package.
 *
 * @author ATehnix <atehnix@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ATehnix\LaravelVkRequester\Contracts\Traits;

use Illuminate\Support\Str;

/**
 * Trait MagicApiMethod
 */
trait MagicApiMethod
{
    protected $apiMethod;

    /**
     * Get API Method by class name.
     *
     * @return string
     */
    protected function getApiMethod()
    {
        if (!isset($this->apiMethod)) {
            $words = explode('_', Str::snake(class_basename($this)), -1);
            $endpoint = strtolower(array_shift($words));
            $action = Str::camel(implode('_', $words)) ?: '*';
            $method = sprintf('%s.%s', $endpoint, $action);
            $this->apiMethod = $endpoint ? $method : 'undefined';
        }

        return $this->apiMethod;
    }
}
