<?php

use Gillyware\Postal\Exceptions\PostalException;
use Gillyware\Postal\Tests\Fixtures\AliasedPacket;
use Gillyware\Postal\Tests\Fixtures\CustomValidationFailurePacket;
use Gillyware\Postal\Tests\Fixtures\CustomValidationPreparationPacket;
use Gillyware\Postal\Tests\Fixtures\ExamplePacket;
use Gillyware\Postal\Tests\Fixtures\ExplicitMergePacket;
use Gillyware\Postal\Tests\Fixtures\ExplicitOnlyPacket;
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

    public function test_it_maps_input_keys_to_aliased_constructor_params()
    {
        $packet = AliasedPacket::from([
            'user_name' => 'Dana',
            'user_email' => 'dana@example.com',
        ]);

        $this->assertEquals('Dana', $packet->name);
        $this->assertEquals('dana@example.com', $packet->email);
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

    public function test_explicit_only_packet_passes_validation()
    {
        $packet = ExplicitOnlyPacket::from(['user_name' => 'alice']);

        $this->assertInstanceOf(ExplicitOnlyPacket::class, $packet);
        $this->assertEquals('alice', $packet->name);
    }

    public function test_explicit_only_packet_fails_validation()
    {
        $this->expectException(ValidationException::class);

        ExplicitOnlyPacket::from(['user_name' => 'charlie']);
    }

    public function test_attribute_and_explicit_rules_merge_pass()
    {
        $packet = ExplicitMergePacket::from(['name' => 'foo']);

        $this->assertInstanceOf(ExplicitMergePacket::class, $packet);
        $this->assertEquals('foo', $packet->name);
    }

    public function test_attribute_and_explicit_rules_merge_fail()
    {
        $this->expectException(ValidationException::class);

        ExplicitMergePacket::from(['name' => 'baz']);
    }
}
