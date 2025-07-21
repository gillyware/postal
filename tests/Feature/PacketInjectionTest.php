<?php

use Gillyware\Postal\Tests\Fixtures\ExamplePacket;
use Illuminate\Support\Facades\Route;

it('injects a packet into a controller action', function () {
    Route::post('/example', function (ExamplePacket $packet) {
        return response()->json($packet->toArray());
    });

    $response = $this->postJson('/example', [
        'name' => 'Charlie',
        'email' => 'charlie@example.com',
    ]);

    $response->assertOk()
        ->assertExactJson([
            'name' => 'Charlie',
            'email' => 'charlie@example.com',
        ]);
});
