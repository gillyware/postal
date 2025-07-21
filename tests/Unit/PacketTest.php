<?php

use Gillyware\Postal\Exceptions\PostalException;
use Gillyware\Postal\Tests\Fixtures\CustomValidationFailurePacket;
use Gillyware\Postal\Tests\Fixtures\CustomValidationPreparationPacket;
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

    public function test_it_allows_custom_validation_preparation_behavior()
    {
        $packet = CustomValidationPreparationPacket::from([
            'name' => 'Alice',
            'email' => 'Alice@Example.com',
        ]);

        $this->assertEquals('ALICE', $packet->name);
        $this->assertEquals('alice@example.com', $packet->email);
    }

    public function test_it_allows_custom_validation_failure_behavior()
    {
        $this->expectException(PostalException::class);
        $this->expectExceptionMessage('Validation failed');

        CustomValidationFailurePacket::from([
            'name' => 'Alice',
            'email' => 'not-an-email',
        ]);
    }
}
