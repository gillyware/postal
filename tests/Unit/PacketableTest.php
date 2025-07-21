<?php

use Gillyware\Postal\Tests\Fixtures\ExamplePacket;
use Gillyware\Postal\Tests\Fixtures\FakeUser;
use Gillyware\Postal\Tests\TestCase;

class PacketableTest extends TestCase
{
    public function test_it_converts_a_model_to_its_packet()
    {
        $user = new FakeUser(['name' => 'Bob', 'email' => 'bob@example.com']);
        $packet = $user->toPacket();

        $this->assertInstanceOf(ExamplePacket::class, $packet);
        $this->assertEquals('Bob', $packet->name);
        $this->assertEquals('bob@example.com', $packet->email);
    }
}
