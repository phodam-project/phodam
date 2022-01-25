<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Types\Builtin;

use InvalidArgumentException;
use Phodam\Types\Builtin\Float\FloatTypeProvider;
use Phodam\Types\Builtin\Float\FloatTypeProviderInterface;
use Phodam\Types\Builtin\Int\IntTypeProvider;
use Phodam\Types\Builtin\Int\IntTypeProviderInterface;
use Phodam\Types\Builtin\String\StringTypeProvider;
use Phodam\Types\Builtin\String\StringTypeProviderInterface;

class BuiltinValueFactory
{
    private const INT_TYPE = "int";
    private const FLOAT_TYPE = "float";
    private const STRING_TYPE = "string";

    /** @var array<string, class-string> */
    private const VALID_BUILTIN_TYPE_PROVIDER_INTERFACES = [
        self::INT_TYPE => IntTypeProviderInterface::class,
        self::FLOAT_TYPE => FloatTypeProviderInterface::class,
        self::STRING_TYPE => StringTypeProviderInterface::class
    ];

    /**
     * @template T of object
     * @var array<BuiltinTypeProvider<T>> $customBuiltinTypeProviders
     */
    private array $customBuiltinTypeProviders = [];

    /**
     * @template T of object
     * @var array<string, BuiltinTypeProvider<T>> $defaultBuiltinTypeProviders
     */
    private array $defaultBuiltinTypeProviders = [];

    public function __construct()
    {
        $this->defaultBuiltinTypeProviders[self::INT_TYPE] = new IntTypeProvider();
        $this->defaultBuiltinTypeProviders[self::FLOAT_TYPE] = new FloatTypeProvider();
        $this->defaultBuiltinTypeProviders[self::STRING_TYPE] = new StringTypeProvider();
    }

    public function registerBuiltinTypeProvider(BuiltinTypeProvider $builtinTypeProvider): self
    {
        foreach (self::VALID_BUILTIN_TYPE_PROVIDER_INTERFACES as $key => $interface) {
            if ($builtinTypeProvider instanceof $interface) {
                $this->customBuiltinTypeProviders[$key] = $builtinTypeProvider;
                return $this;
            }
        }

        throw new InvalidArgumentException("Provider is not for a valid builtin class");
    }

    /**
     * @return int
     */
    public function createInt(): int
    {
        return (int) $this->createBuiltinValue(self::INT_TYPE);
    }

    /**
     * @return float
     */
    public function createFloat(): float
    {
        return (float) $this->createBuiltinValue(self::FLOAT_TYPE);
    }

    /**
     * @return string
     */
    public function createString(): string
    {
        return $this->createBuiltinValue(self::STRING_TYPE);
    }

    /**
     * // TODO: This really returns "mixed" but PHP7.4 won't let us do that.
     * @param string $type
     * @return mixed
     */
    private function createBuiltinValue(string $type)
    {
        if (array_key_exists($type, $this->customBuiltinTypeProviders)) {
            return $this->customBuiltinTypeProviders[$type]->create();
        }
        return $this->defaultBuiltinTypeProviders[$type]->create();
    }
}
