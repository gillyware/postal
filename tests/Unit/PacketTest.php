<?php

use Gillyware\Postal\Tests\Fixtures\ExamplePacket;
use Illuminate\Validation\ValidationException;

it('validates & hydrates ExamplePacket', function () {
    $packet = ExamplePacket::from([
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ]);

    expect($packet)->toBeInstanceOf(ExamplePacket::class)
        ->and($packet->name)->toBe('Alice')
        ->and($packet->email)->toBe('alice@example.com');
});

it('throws on invalid data', function () {
    ExamplePacket::from([
        'name' => 'Alice',
        'email' => 'not-an-email',
    ]);
})->throws(ValidationException::class);
