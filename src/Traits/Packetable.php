<?php

namespace Gillyware\Postal\Traits;

use Gillyware\Postal\Contracts\PacketableInterface;
use Gillyware\Postal\Exceptions\PostalException;
use Gillyware\Postal\Packet;

/**
 * @template T of Packet
 */
trait Packetable
{
    /**
     * Convert the current model into its paired Packet.
     *
     * @return T
     *
     * @throws PostalException
     */
    public function toPacket(): Packet
    {
        if (! $this instanceof PacketableInterface) {
            throw new PostalException(
                'Cannot create a packet unless the class implements PacketableInterface.'
            );
        }

        $packetClass = static::packetClass();

        if (! class_exists($packetClass) || ! is_subclass_of($packetClass, Packet::class)) {
            throw new PostalException("{$packetClass} is not a valid Packet subclass.");
        }

        /** @var class-string<T> $packetClass */
        return $packetClass::from($this->packetData());
    }

    /**
     * Resolve the fully‑qualified name of the paired Packet class.
     *
     * @return class-string<T>
     */
    protected static function packetClass(): string
    {
        $base = class_basename(static::class);

        return "App\\Packets\\{$base}Packet";
    }

    /**
     * Get the data used to construct the packet.
     */
    protected function packetData(): array
    {
        return $this->toArray();
    }
}
