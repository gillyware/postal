<?php

use Gillyware\Postal\Tests\Fixtures\AliasedPacket;

it('maps input keys to aliased constructor params', function () {
    $packet = AliasedPacket::from([
        'user_name' => 'Dana',
        'user_email' => 'dana@example.com',
    ]);

    expect($packet->name)->toBe('Dana')
        ->and($packet->email)->toBe('dana@example.com');
});
