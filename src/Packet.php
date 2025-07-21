<?php

namespace Gillyware\Postal;

use Gillyware\Postal\Attributes\Field;
use Gillyware\Postal\Attributes\Rule;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ReflectionClass;

/**
 * @template T of Packet
 */
abstract class Packet implements Arrayable
{
    /**
     * Build & validate the packet.
     *
     * @throws ValidationException
     */
    public static function from(Request|array $source): static
    {
        $data = $source instanceof Request ? $source->all() : $source;

        $validator = Validator::make($data, static::rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        /** @var array<string,mixed> $validated */
        $validated = $validator->validated();

        $args = static::mapConstructorArgs($validated);

        /** @var class-string<T> $calledClass */
        $calledClass = static::class;

        /** @psalm-var T $instance */
        $instance = new $calledClass(...$args);

        return $instance;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Collect validation rules declared via attributes.
     *
     * @return array<string, string|array>
     */
    private static function rules(): array
    {
        static $cache = [];

        return $cache[static::class] ??= (function () {
            $rules = [];
            $ref = new ReflectionClass(static::class);

            foreach ($ref->getConstructor()?->getParameters() ?? [] as $param) {
                $ruleAttr = $param->getAttributes(Rule::class)[0] ?? null;
                if (! $ruleAttr) {
                    continue;
                }

                $fieldAttr = $param->getAttributes(Field::class)[0] ?? null;
                $name = $fieldAttr?->newInstance()->name ?? $param->getName();

                /** @var Rule $rule */
                $rule = $ruleAttr->newInstance();
                $rules[$name] = $rule->rules;
            }

            return $rules;
        })();
    }

    /**
     * Align validated data with constructor param order.
     *
     * @param  array<string,mixed>  $validated
     * @return array<int,mixed>
     */
    private static function mapConstructorArgs(array $validated): array
    {
        $args = [];
        $ref = new ReflectionClass(static::class);

        foreach ($ref->getConstructor()?->getParameters() ?? [] as $param) {
            $fieldAttr = $param->getAttributes(Field::class)[0] ?? null;
            $key = $fieldAttr?->newInstance()->name ?? $param->getName();
            $args[] = $validated[$key] ?? null;
        }

        return $args;
    }
}
