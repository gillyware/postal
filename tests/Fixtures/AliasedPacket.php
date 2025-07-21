<?php

namespace Gillyware\Postal\Tests\Fixtures;

use Gillyware\Postal\Attributes\Field;
use Gillyware\Postal\Attributes\Rule;
use Gillyware\Postal\Packet;

final class AliasedPacket extends Packet
{
    public function __construct(
        #[Field('user_name'), Rule('required|string|max:255')]
        public readonly string $name,

        #[Field('user_email'), Rule('required|email')]
        public readonly string $email,
    ) {}
}
