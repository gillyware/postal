<?php

namespace Gillyware\Postal\Tests\Fixtures;

use Gillyware\Postal\Attributes\Rule;
use Gillyware\Postal\Packet;

final class CustomValidationPreparationPacket extends Packet
{
    public function __construct(
        #[Rule('required|string|max:255')] public readonly string $name,
        #[Rule('required|email')] public readonly string $email,
    ) {}

    protected static function prepareForValidation(array $data): array
    {
        return [
            'name' => strtoupper($data['name']),
            'email' => strtolower($data['email']),
        ];
    }
}
