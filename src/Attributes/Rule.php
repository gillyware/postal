<?php

namespace Gillyware\Postal\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Rule
{
    public function __construct(public string|array $rules) {}
}
