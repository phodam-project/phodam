<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Store;

use Phodam\Provider\ProviderInterface;

interface ProviderStoreInterface
{
    public function hasNamedProvider(string $type, string $name): bool;

    public function hasDefaultProvider(string $type): bool;

    /**
     * @throws ProviderNotFoundException
     */
    public function findNamedProvider(string $type, string $name): ProviderInterface;

    /**
     * @throws ProviderNotFoundException
     */
    public function findDefaultProvider(string $type): ProviderInterface;

    /**
     * @return array<string, ProviderInterface>
     */
    public function findAllNamedProvidersForType(string $type): array;

    /**
     * @throws ProviderConflictException
     */
    public function registerNamedProvider(
        string $type,
        string $name,
        ProviderInterface $provider
    ): void;

    /**
     * @throws ProviderConflictException
     */
    public function registerDefaultProvider(
        string $type,
        ProviderInterface $provider
    ): void;

    /**
     * @throws ProviderNotFoundException
     */
    public function deregisterNamedProvider(string $type, string $name): void;

    /**
     * @throws ProviderNotFoundException
     */
    public function deregisterDefaultProvider(string $type): void;
}
