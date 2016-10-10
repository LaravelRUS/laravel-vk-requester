<?php
/**
 * This file is part of laravel-vk-requester package.
 *
 * @author ATehnix <atehnix@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ATehnix\LaravelVkRequester\Jobs;

use ATehnix\LaravelVkRequester\Models\VkRequest;
use ATehnix\VkClient\Client;
use ATehnix\VkClient\Requests\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class Send
 */
class Send implements ShouldQueue
{
    use Queueable;
    
    /**
     * Default delay before sending request (in milliseconds)
     */
    const DEFAULT_DELAY = 350;

    /**
     * API Client
     *
     * @var Client
     */
    protected $api;

    /**
     * Instance of request
     *
     * @var VkRequest
     */
    protected $request;

    /**
     * Data of response
     *
     * @var array
     */
    protected $response;

    /**
     * Create a new job instance.
     *
     * @param VkRequest $request
     */
    public function __construct(VkRequest $request)
    {
        $this->request = $request;
        $request->delete();
    }

    /**
     * Execute the job.
     *
     * @param Client $api Pre-configured instance of the Client
     */
    public function handle(Client $api)
    {
        // Initialize
        $this->api = $api;
        $this->api->setPassError(config('vk-requester.pass_error', true));

        // Processing
        $this->sendToApi();
        $this->fireEvent();
    }

    /**
     * Send request to Vk.com API
     */
    protected function sendToApi()
    {
        usleep(config('vk-requester.delay', self::DEFAULT_DELAY) * 1000);
        $clientRequest = new Request($this->request->method, $this->request->parameters, $this->request->token);
        $clientResponse = $this->api->send($clientRequest);
        $this->response = $this->getResponse($clientResponse);
    }

    /**
     * Get data array from response
     *
     * @param array $clientResponse
     * @return array
     */
    protected function getResponse(array $clientResponse)
    {
        if (isset($clientResponse['error'])) {
            return $clientResponse['error'];
        }

        return isset($clientResponse['response']) ? $clientResponse['response'] : [];
    }

    /**
     * Fire an event for response
     */
    private function fireEvent()
    {
        $status = isset($this->response['error_code']) ? VkRequest::STATUS_FAIL : VkRequest::STATUS_SUCCESS;
        $event = sprintf(VkRequest::EVENT_FORMAT, $status, $this->request->method, $this->request->tag);
        event($event, [$this->request, $this->response]);
    }
}
