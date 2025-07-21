<?php

use Gillyware\Postal\Tests\Fixtures\AliasedPacket;
use Gillyware\Postal\Tests\TestCase;

class FieldAliasTest extends TestCase
{
    public function test_it_maps_input_keys_to_aliased_constructor_params()
    {
        $packet = AliasedPacket::from([
            'user_name' => 'Dana',
            'user_email' => 'dana@example.com',
        ]);

        $this->assertEquals('Dana', $packet->name);
        $this->assertEquals('dana@example.com', $packet->email);
    }
}
