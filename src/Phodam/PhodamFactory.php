<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

use Exception;
use Phodam\Types\Builtin\BuiltinTypeProvider;
use Phodam\Types\Builtin\BuiltinValueFactory;

class PhodamFactory implements PhodamFactoryInterface
{
    private BuiltinValueFactory $builtinValueFactory;

    public function __construct(
        BuiltinValueFactory $builtinValueFactory = null
    ) {
        $this->builtinValueFactory = $builtinValueFactory ?? new BuiltinValueFactory();
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $overrides
     * @return T|null
     */
    public function create(string $class, array $overrides = [])
    {
        /** @var T $val */
        $val = null;

        // check custom providers
        // check some other notations
        // check builtintypeproviders

        return $val;
    }

    public function createInt(): int
    {
        return $this->builtinValueFactory->createInt();
    }

    public function createFloat(): float
    {
        return $this->builtinValueFactory->createFloat();
    }

    public function createString(): string
    {
        return $this->builtinValueFactory->createString();
    }

    /**
     * @param BuiltinTypeProvider $builtinTypeProvider
     * @return $this
     */
    public function registerBuiltinTypeProvider(
        BuiltinTypeProvider $builtinTypeProvider
    ): self {
        $this->builtinValueFactory->registerBuiltinTypeProvider($builtinTypeProvider);
        return $this;
    }

    public function createNamedArray(string $name, array $overrides = []): array
    {
        throw new Exception("Not Yet Implemented");
    }

    public function createAssociativeArray(string $class, array $overrides): array
    {
        throw new Exception("Not Yet Implemented");
    }
}
