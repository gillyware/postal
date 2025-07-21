<?php

use Gillyware\Postal\Tests\Fixtures\ExamplePacket;
use Gillyware\Postal\Tests\Fixtures\FakeUser;

it('converts a model to its packet', function () {
    $user = new FakeUser(['name' => 'Bob', 'email' => 'bob@example.com']);
    $packet = $user->toPacket();

    expect($packet)->toBeInstanceOf(ExamplePacket::class)
        ->and($packet->name)->toBe('Bob')
        ->and($packet->email)->toBe('bob@example.com');
});
