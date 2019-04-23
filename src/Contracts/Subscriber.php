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
use ATehnix\VkClient\Client;
use ATehnix\VkClient\Exceptions\VkException;
use Illuminate\Events\Dispatcher;

/**
 * @inheritdoc
 */
abstract class Subscriber
{
    use MagicApiMethod;

    /**
     * Expected tag
     *
     * @var string
     */
    protected $tag = 'default';

    /**
     * Handling success event
     *
     * @param VkRequest $request
     * @param mixed $response
     */
    abstract public function onSuccess(VkRequest $request, $response);

    /**
     * Handling fail event
     *
     * @param VkRequest $request
     * @param array $error
     */
    public function onFail(VkRequest $request, array $error)
    {
        //
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            sprintf(VkRequest::EVENT_FORMAT, VkRequest::STATUS_SUCCESS, $this->getApiMethod(), $this->tag),
            static::class.'@onSuccess'
        );
        $events->listen(
            sprintf(VkRequest::EVENT_FORMAT, VkRequest::STATUS_FAIL, $this->getApiMethod(), $this->tag),
            static::class.'@onFail'
        );
    }

    /**
     * Convert error to Exception object
     *
     * @param array $error
     * @return VkException
     */
    protected function toException(array $error)
    {
        return Client::toException($error);
    }
}
