<?php
/**
 * This file is part of laravel-vk-requester package.
 *
 * @author ATehnix <atehnix@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ATehnix\LaravelVkRequester\Commands;

use ATehnix\LaravelVkRequester\Jobs\SendBatch;
use ATehnix\LaravelVkRequester\Models\VkRequest;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;

class VkRequesterGeneratorCommand extends Command
{
    use DispatchesJobs;

    /**
     * Default number of nested requests
     */
    const NUMBER_OF_REQUESTS = 25;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk-requester:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate request jobs in queue.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        VkRequest::all()->groupBy('token')->each(function (Collection $requests, $token) {
            foreach ($requests->chunk(self::NUMBER_OF_REQUESTS) as $chunkRequests) {
                $this->dispatch(new SendBatch($chunkRequests, $token));
            }
        });
    }
}
