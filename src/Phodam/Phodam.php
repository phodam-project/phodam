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
     * @param array<string, mixed> $config provider-specific information. an
     *     open-ended array for the provider to pass information along
     * @return array<string, mixed>
     */
    public function createArray(
        string $name,
        array $overrides = [],
        array $config = []
    ): array {
        return $this->typeProviderFactory
            ->getArrayProvider($name)
            ->create($overrides, $config);
    }

    /**
     * @template T
     * @param class-string<T> $class class to create
     * @param string|null $name the name of the class provider
     * @param array<string, mixed> $overrides values to override
     * @param array<string, mixed> $config provider-specific information. an
     *     open-ended array for the provider to pass information along
     * @return T
     */
    public function create(
        string $class,
        string $name = null,
        array $overrides = [],
        array $config = []
    ) {
        return $this->typeProviderFactory
            ->getClassProvider($class, $name)
            ->create($overrides, $config);
    }

    /**
     * Create a random float or a named float
     *
     * @param string|null $name the name of the float if any
     * @param array<string, mixed> $config provider-specific information. an
     *     open-ended array for the provider to pass information along
     * @return float
     */
    public function createFloat(
        string $name = null,
        array $config = []
    ): float {
        return $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_FLOAT,
            $name
        )
            ->create([], $config);
    }

    /**
     * Create a random int or named int
     *
     * @param string|null $name the name of the int if any
     * @param array<string, mixed> $config provider-specific information. an
     *     open-ended array for the provider to pass information along
     * @return int
     */
    public function createInt(
        string $name = null,
        array $config = []
    ): int {
        return $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_INT,
            $name
        )
            ->create([], $config);
    }

    /**
     * Create a random string or named string
     *
     * @param string|null $name the name of the string if any
     * @param array<string, mixed> $config provider-specific information. an
     *     open-ended array for the provider to pass information along
     * @return string
     */
    public function createString(
        string $name = null,
        array $config = []
    ): string {
        return $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_STRING,
            $name
        )
            ->create([], $config);
    }
}
