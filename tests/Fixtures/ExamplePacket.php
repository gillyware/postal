<?php

namespace Gillyware\Postal\Tests\Fixtures;

use Gillyware\Postal\Attributes\Rule;
use Gillyware\Postal\Packet;

final class ExamplePacket extends Packet
{
    public function __construct(
        #[Rule('required|string|max:255')] public readonly string $name,
        #[Rule('required|email')] public readonly string $email,
    ) {}
}
