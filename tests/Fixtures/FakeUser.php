<?php

namespace Gillyware\Postal\Tests\Fixtures;

use Gillyware\Postal\Contracts\PacketableInterface;
use Gillyware\Postal\Traits\Packetable;
use Illuminate\Database\Eloquent\Model;

class FakeUser extends Model implements PacketableInterface
{
    /** @use Packetable<ExamplePacket> */
    use Packetable;

    public $timestamps = false;

    protected $guarded = [];

    protected static function packetClass(): string
    {
        return ExamplePacket::class;
    }
}
