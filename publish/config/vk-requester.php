<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | Version Vkontakte API, which must be used in request.
    |
    */

    'version' => '5.53',

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    |
    | To receive necessary permissions during authorization, fill scope
    | parameter containing names of the required permissions as array items.
    |
    | See more at: https://vk.com/dev/permissions
    |
    */

    'scope' => [
        'offline',
    ],

    /*
    |--------------------------------------------------------------------------
    | Table name
    |--------------------------------------------------------------------------
    |
    | The name of the table for temporary storage requests.
    |
    */

    'table' => 'vk_requests',

    /*
    |--------------------------------------------------------------------------
    | Delay before request
    |--------------------------------------------------------------------------
    |
    | The delay before sending the request in milliseconds.
    |
    */

    'delay' => 350,

    /*
    |--------------------------------------------------------------------------
    | Pass errors
    |--------------------------------------------------------------------------
    |
    | Pass API errors without throwing exception.
    | If false - throw VkException when an API errors occurs.
    |
    */

    'pass_error' => true,

    /*
    |--------------------------------------------------------------------------
    | Auto dispatch
    |--------------------------------------------------------------------------
    |
    | Auto dispatch of job for sending batch requests.
    |
    | When "auto_dispatch" option is false, you can manually define
    | VkRequesterGeneratorCommand command in task scheduler. Or you may pass
    | array of VkRequests to the SendBatch job and manually dispatch it.
    |
    */

    'auto_dispatch' => true,

];
