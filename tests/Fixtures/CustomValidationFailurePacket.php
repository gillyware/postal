<?php

namespace Gillyware\Postal\Tests\Fixtures;

use Gillyware\Postal\Attributes\Rule;
use Gillyware\Postal\Exceptions\PostalException;
use Gillyware\Postal\Packet;
use Illuminate\Contracts\Validation\Validator;

final class CustomValidationFailurePacket extends Packet
{
    public function __construct(
        #[Rule('required|string|max:255')] public readonly string $name,
        #[Rule('required|email')] public readonly string $email,
    ) {}

    protected static function failedValidation(Validator $validator): void
    {
        throw new PostalException('Validation failed.');
    }
}
