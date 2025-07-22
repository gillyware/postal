<?php

namespace Gillyware\Postal\Tests\Fixtures;

use Gillyware\Postal\Attributes\Rule;
use Gillyware\Postal\Packet;

final class ExplicitMergePacket extends Packet
{
    public function __construct(
        #[Rule(['required', 'string'])]
        public readonly string $name,
    ) {}

    /** Merge extra rule with existing attribute rule */
    protected static function explicitRules(): array
    {
        return [
            'name' => 'in:foo,bar',
        ];
    }
}
