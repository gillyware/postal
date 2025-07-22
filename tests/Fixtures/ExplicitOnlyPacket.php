<?php

namespace Gillyware\Postal\Tests\Fixtures;

use Gillyware\Postal\Attributes\Field;
use Gillyware\Postal\Packet;

final class ExplicitOnlyPacket extends Packet
{
    public function __construct(
        #[Field('user_name')]
        public readonly string $name,
    ) {}

    /** Only explicit rules, nothing from attributes */
    protected static function explicitRules(): array
    {
        return [
            'user_name' => ['required', 'string', 'in:alice,bob'],
        ];
    }
}
