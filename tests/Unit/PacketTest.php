<?php

use Gillyware\Postal\Tests\Fixtures\ExamplePacket;
use Gillyware\Postal\Tests\TestCase;
use Illuminate\Validation\ValidationException;

class PacketTest extends TestCase
{
    public function test_it_validates_and_hydrates_example_packet()
    {
        $packet = ExamplePacket::from([
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $this->assertInstanceOf(ExamplePacket::class, $packet);
        $this->assertEquals('Alice', $packet->name);
        $this->assertEquals('alice@example.com', $packet->email);
    }

    public function test_it_throws_on_invalid_data()
    {
        $this->expectException(ValidationException::class);

        ExamplePacket::from([
            'name' => 'Alice',
            'email' => 'not-an-email',
        ]);
    }
}
