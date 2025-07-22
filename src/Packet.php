<?php

namespace Gillyware\Postal;

use Gillyware\Postal\Attributes\Field;
use Gillyware\Postal\Attributes\Rule;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
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
        $sourceData = $source instanceof Request ? $source->all() : $source;

        $validationData = static::prepareForValidation($sourceData);

        $validator = ValidatorFacade::make($validationData, static::rules());

        if ($validator->fails()) {
            static::failedValidation($validator);
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

    /**
     * Get an associative array of the class variables.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Prepare data to be validated.
     */
    protected static function prepareForValidation(array $data): array
    {
        return $data;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @throws ValidationException
     */
    protected static function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator);
    }

    /**
     * Explicitly supply rules. Intended for rules that cannot be resolved at compile-time.
     */
    protected static function explicitRules(): array
    {
        return [];
    }

    /**
     * Final rule set used by ::from().
     * Combines attribute rules and explicit rules.
     */
    private static function rules(): array
    {
        $rules = static::attributeRules();
        $explicit = static::explicitRules();
        $aliasMap = static::parameterAliasMap();

        foreach ($explicit as $key => $value) {
            $inputKey = $aliasMap[$key] ?? $key;

            if (isset($rules[$inputKey])) {
                // Merge arrays / pipe-delimited strings into array
                $rules[$inputKey] = array_merge(
                    (array) $rules[$inputKey],
                    (array) $value
                );
            } else {
                $rules[$inputKey] = $value;
            }
        }

        return $rules;
    }

    /**
     * Collect validation rules declared via attributes.
     *
     * @return array<string, string|array>
     */
    private static function attributeRules(): array
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
     * Map constructor parameter name to alias (if #[Field] present).
     *
     * @return array<string,string>
     */
    private static function parameterAliasMap(): array
    {
        $map = [];
        $ref = new ReflectionClass(static::class);

        foreach ($ref->getConstructor()?->getParameters() ?? [] as $param) {
            $fieldAttr = $param->getAttributes(Field::class)[0] ?? null;
            if ($fieldAttr) {
                $map[$param->getName()] = $fieldAttr->newInstance()->name;
            }
        }

        return $map;
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
