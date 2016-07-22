<?php

namespace ATehnix\LaravelVkRequester\Commands;

use ATehnix\LaravelVkRequester\Jobs\SendBatch;
use ATehnix\LaravelVkRequester\Models\VkRequest;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;

class VkRequesterGeneratorCommand extends Command
{
    const NUMBER_OF_REQUESTS = 25;
    
    use DispatchesJobs;
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
     *
     * @return mixed
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
