# Installation

- [Install Postal](#install-postal)
- [Setting Up Packets](#setting-up-packets)
- [Instantiating Packets](#instantiating-packets)
- [Accessing Packet Data](#accessing-packet-data)

<a name="install-postal"></a>
## Install Postal

You may install Postal into your project using the Composer package manager:

```shell
composer require gillyware/postal
```

<a name="setting-up-packets"></a>
# Setting Up Packets

Packets are **immutable DTOs** that live in `app/Packets`.  
Each packet is a *single PHP class* whose constructor parameters define:

* **The data shape** (types + property names)  
* **Validation rules** via `#[Rule]` attributes  
* Optional **input aliases** via `#[Field]` attributes

## 1 . Create the Packet Class

```php
<?php

namespace App\Packets;

use Gillyware\Postal\Packet;

final class StoreUserPacket extends Packet
{
    //
}
```

## 2. Add Properties and Rules

```php
use Gillyware\Postal\Attributes\Rule;

final class StoreUserPacket extends Packet
{
    public function __construct(
        #[Rule('required|string|max:255')]
        public readonly string $name,

        #[Rule('required|email')]
        public readonly string $email,

        #[Rule('nullable|boolean')]
        public readonly ?bool $subscribe = false,
    ) {}
}
```

* Visibility – `public readonly` is recommended.

* Defaults – provide a default value to make the field optional.

## 3 . (Optional) Alias Input Keys

You want to map an incoming `user_name` field to the `name` constructor parameter:

```php
use Gillyware\Postal\Attributes\{Rule, Field};

#[Field('user_name'), Rule('required|string|max:255')]
public readonly string $name,
```

<a name="instantiating-packets"></a>
# Instantiating Packets

Packets are validated when constructed, and a `ValidationException` is thrown if validation fails.

### 4.1 Controller Injection

An packet injected into a controller action will be constructed from `$request->all()`.

```php
public function store(StoreUserPacket $packet)
{
    User::create($packet->toArray());
}
```

### 4.1 `Packetable` Trait

You may want to create a packet from an existing class, like an Eloquent Model. This class must implement `PacketableInterface` and use the `Packetable` trait:

```php
use App\Packets\PermissionPacket;
use Gillyware\Postal\Contracts\PacketableInterface;
use Gillyware\Postal\Traits\Packetable;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model implements PacketableInterface
{
    /** @use Packetable<PermissionPacket> */
    use Packetable;

    // You may optionally make the packet class explicit.
    // The default is App\Packets\{className}Packet (e.g. App\Packets\PermissionPacket).
    protected static function packetClass(): string {...}

    // You may optionally specify the data used to construct the packet.
    // The default is $this->toArray().
    protected function packetData(): array {...}
}
```

You may then instantiate a packet from this model instance with:

```php
$permission = Permission::query()->find(1);

$permission->toPacket();
```

### 4.2 Manual Construction

Packets may also be constructed manually with a request or array.

```php
$packet = StoreUserPacket::from($request);

$packet = StoreUserPacket::from($request->all());
```

<a name="accessing-packet-data"></a>
## 5 . Accessing Data

You may access individual packet attributes or get an array with all validated attributes:

```php
$packet->name;

$packet->toArray();
```