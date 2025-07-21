<?php

use Gillyware\Postal\Tests\Fixtures\ExamplePacket;
use Gillyware\Postal\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class PacketInjectionTest extends TestCase
{
    public function test_it_injects_a_packet_into_a_controller_action()
    {
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
    }
}
