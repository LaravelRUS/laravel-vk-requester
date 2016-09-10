<?php
/**
 * This file is part of laravel-vk-requester package.
 *
 * @author ATehnix <atehnix@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ATehnix\LaravelVkRequester\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VkRequest
 *
 * @property int $id
 * @property string $method
 * @property array $parameters
 * @property string $token
 * @property string $tag
 * @property array $context
 */
class VkRequest extends Model
{
    /**
     * Default table for associating with requests
     */
    const DEFAULT_TABLE = 'vk_requests';

    /**
     * Format for name of events
     */
    const EVENT_FORMAT = 'vk-requester.%s: %s #%s';

    /**
     * Name of fail status
     */
    const STATUS_FAIL = 'fail';

    /**
     * Name of success status
     */
    const STATUS_SUCCESS = 'success';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'method',
        'parameters',
        'token',
        'tag',
        'context',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'parameters' => 'array',
        'context'    => 'array',
    ];

    /**
     * Create a new VkRequest instance.
     *
     * @param  array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('vk-requester.table', self::DEFAULT_TABLE);

        parent::__construct($attributes);
    }
}
