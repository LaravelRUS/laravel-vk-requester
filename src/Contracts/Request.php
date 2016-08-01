<?php
/**
 * This file is part of laravel-vk-requester package.
 *
 * @author ATehnix <atehnix@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ATehnix\LaravelVkRequester\Contracts;

use ATehnix\LaravelVkRequester\Contracts\Traits\MagicApiMethod;
use ATehnix\LaravelVkRequester\Models\VkRequest;

/**
 * @inheritdoc
 */
abstract class Request extends VkRequest
{
    use MagicApiMethod;

    /**
     * @inheritdoc
     */
    protected static function boot()
    {
        static::saving(function (self $request) {
            $request->setAttribute('method', $request->getApiMethod());
        });
    }

    /**
     * Setter for Parameters
     *
     * @param array $parameters
     */
    public function setParametersAttribute(array $parameters)
    {
        $defaultParameters = $this->getDefaultParameters();
        $this->attributes['parameters'] = json_encode(
            array_merge($defaultParameters, $parameters)
        );
    }

    /**
     * Define default parameters for request
     *
     * @return array
     */
    abstract protected function getDefaultParameters();
}
