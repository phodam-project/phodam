<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

use Phodam\Provider\TypeProviderConfig;
use Phodam\Provider\TypeProviderFactory;

class Phodam
{
    private TypeProviderFactory $typeProviderFactory;

    public function __construct(
        TypeProviderFactory $typeProviderFactory = null
    ) {
        if ($typeProviderFactory == null) {
            $typeProviderFactory = new TypeProviderFactory();
        }
        $this->typeProviderFactory = $typeProviderFactory;
    }

    /**
     * Registers a TypeProvider using a TypeProviderConfig
     *
     * @param TypeProviderConfig $config
     * @return self
     */
    public function registerTypeProviderConfig(TypeProviderConfig $config): self
    {
        $this->typeProviderFactory->registerTypeProviderConfig($config);
        return $this;
    }

    /**
     * Create a named associative array
     *
     * @param string $name the name of the array
     * @param array<string, mixed> $overrides values to override in the array
     * @return array<string, mixed>
     */
    public function createArray(string $name, array $overrides = []): array
    {
        return $this->typeProviderFactory
            ->getArrayProvider($name)
            ->create($overrides);
    }

    /**
     * @template T
     * @param class-string<T> $class class to create
     * @param string|null $name the name of the class provider
     * @param array<string, mixed> $overrides values to override
     * @return T
     */
    public function create(
        string $class,
        string $name = null,
        array $overrides = []
    ) {
        return $this->typeProviderFactory
            ->getClassProvider($class, $name)
            ->create($overrides);
    }

    /**
     * Create a random float or a named float
     *
     * @param string|null $name the name of the float if any
     * @return float
     */
    public function createFloat(string $name = null): float
    {
        return $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_STRING,
            $name
        )
            ->create();
    }

    /**
     * Create a random int or named int
     *
     * @param string|null $name the name of the int if any
     * @return int
     */
    public function createInt(string $name = null): int
    {
        return $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_INT,
            $name
        )
            ->create();
    }

    /**
     * Create a random string or named string
     *
     * @param string|null $name the name of the string if any
     * @return string
     */
    public function createString(string $name = null): string
    {
        return $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_STRING,
            $name
        )
            ->create();
    }
}
