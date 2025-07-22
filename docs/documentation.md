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
## Setting Up Packets

Packets are **immutable DTOs** that live in `app/Packets`.  
Each packet is a *single PHP class* whose constructor parameters define:

* **The data shape** (types + property names)  
* **Validation rules** via `#[Rule]` attributes  
* Optional **input aliases** via `#[Field]` attributes

<a name="create-the-packet-class"></a>
### 1 . Create the Packet Class

```php
<?php

namespace App\Packets;

use Gillyware\Postal\Packet;

final class StoreUserPacket extends Packet
{
    // ...
}
```

<a name="add-properties-and-rules"></a>
### 2. Add Properties and Rules

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

> [!WARNING]
> Rules defined using attributes are resolved at compile-time, so they must be a string (including constants) or an array of strings. If your validation rule must resolve at runtime (rule depends on a config value, rule defined using Laravel's Rule class, etc.), then that rule must be defined as an [explicit rule](#explicit-rules).

<a name="alias-input-keys"></a>
### 3. Alias Input Keys

You may want to map an input data field to a specific packet attribute.

Suppose you want to map an incoming `user_name` field to the `name` constructor parameter:

```php
use Gillyware\Postal\Attributes\{Rule, Field};

#[Field('user_name'), Rule('required|string|max:255')]
public readonly string $name,
```

<a name="explicit-rules"></a>
### 4. Explicit Rules

Most validation can live in `#[Rule]` attributes, but sometimes a rule
can’t be expressed at compile‑time (e.g. needs a config value)
Use the **`explicitRules()`** hook for those cases.

```php
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule as ValidationRule;
use Gillyware\Postal\Packet;

final class StorePermissionPacket extends Packet
{
    public function __construct(
        #[Rule(['required', 'string', 'max:255'])]
        public readonly string $name,
    ) {}

    /**
     * Add or replace validation rules that depend on runtime data.
     */
    protected static function explicitRules(): array
    {
        $table = Config::get('gatekeeper.tables.permissions');

        return [
            // Merges with attribute rules.
            'name' => [
                ValidationRule::unique($table, 'name')->withoutTrashed(),
            ],
        ];
    }
}
```

> [!NOTE]
> If a constructor parameter uses `#[Field('input_key')]`, key your explicit rules array by the input key (`input_key`) not the property name.

<a name="prepare-for-validation"></a>
### 5. Prepare for Validation

You may want to manipulate input data before it's validated.

The manipulated data that gets validated will be the data used to hydrate the packet.

Override the `prepareForValidation` function, the parameter is the source data array:

```php
protected static function prepareForValidation(array $data): array
{
    return [
        'name' => ucfirst($data['name']),
        'email' => strtolower($data['email']),
    ];
}
```

<a name="failed-validation"></a>
### 6. Failed Validation

You may want to specify behavior for validation failure. The default behavior throws a `ValidationException`.

Override the `failedValidation` function, the parameter is the validator instance:

```php
use Illuminate\Contracts\Validation\Validator;
use Gillyware\Postal\Exceptions\PostalException;

protected static function failedValidation(Validator $validator): void
{
    throw new PostalException('Validation failed.');
}
```

<a name="instantiating-packets"></a>
## Instantiating Packets

Packets are validated when instantiated in any of the following ways.

<a name="controller-injection"></a>
### Controller Injection

A packet injected into a controller action will be constructed from `$request->all()`.

```php
public function store(StoreUserPacket $packet)
{
    User::create($packet->toArray());
}
```

<a name="packetable-trait"></a>
### `Packetable` Trait

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
    protected static function packetClass(): string
    {
        return \App\Packets\CustomPermissionPacket::class;
    }

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

<a name="manual-construction"></a>
### Manual Construction

Packets may also be constructed manually with a request or array.

```php
$packet = StoreUserPacket::from($request);

$packet = StoreUserPacket::from($request->all());
```

<a name="accessing-packet-data"></a>
## Accessing Data

You may access individual packet attributes or get an array with all validated attributes:

```php
$packet->name;

$packet->toArray();
```