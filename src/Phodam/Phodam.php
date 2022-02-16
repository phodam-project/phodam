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
     * @return array<string, mixed>
     */
    public function createArray(string $name): array
    {
        $typeProvider = $this->typeProviderFactory->getArrayProvider($name);
        return [];
    }

    /**
     * Create a random float or a named float
     *
     * @param string|null $name the name of the float if any
     * @return float
     */
    public function createFloat(string $name = null): float
    {
        $typeProvider = $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_STRING,
            $name
        );
        return 1.0;
    }

    /**
     * Create a random int or named int
     *
     * @param string|null $name the name of the int if any
     * @return int
     */
    public function createInt(string $name = null): int
    {
        $typeProvider = $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_INT,
            $name
        );
        return 1;
    }

    /**
     * Create a random string or named string
     *
     * @param string|null $name the name of the string if any
     * @return string
     */
    public function createString(string $name = null): string
    {
        $typeProvider = $this->typeProviderFactory->getPrimitiveProvider(
            PhodamTypes::PRIMITIVE_STRING,
            $name
        );
        return "";
    }
}
