<?php

use ATehnix\LaravelVkRequester\Models\VkRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVkRequesterTables extends Migration
{
    private $table;

    /**
     * CreateVkRequesterTables constructor.
     */
    public function __construct()
    {
        $this->table = config('vk-requester.table', VkRequest::DEFAULT_TABLE);
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('method');
            $table->text('parameters');
            $table->string('token');
            $table->string('tag')->default('default');
            $table->text('context');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop($this->table);
    }
}
