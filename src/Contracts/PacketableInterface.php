<?php

namespace Gillyware\Postal\Contracts;

use Gillyware\Postal\Packet;
use Illuminate\Contracts\Support\Arrayable;

interface PacketableInterface extends Arrayable
{
    /**
     * Convert the implementing object into its paired Packet.
     */
    public function toPacket(): Packet;
}
