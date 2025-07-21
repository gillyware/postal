<?php

namespace Gillyware\Postal\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Field
{
    public function __construct(public string $name) {}
}
