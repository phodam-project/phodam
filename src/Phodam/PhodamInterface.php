<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

interface PhodamInterface
{
    /**
     * Create a named associative array
     *
     * @param string $name the name of the array
     * @param ?array<string, mixed> $overrides values to override in the array
     * @param ?array<string, mixed> $config provider-specific information. an
     *     open-ended array for the provider to pass information along
     * @return array<string, mixed>
     */
    public function createArray(
        string $name,
        ?array $overrides = null,
        ?array $config = null
    ): array;

    /**
     * @param string $type type to create
     * @param string|null $name the name of the class provider
     * @param ?array<string, mixed> $overrides values to override
     * @param ?array<string, mixed> $config provider-specific information. an
     *     open-ended array for the provider to pass information along
     * @return mixed
     */
    public function create(
        string $type,
        ?string $name = null,
        ?array $overrides = null,
        ?array $config = null
    );
}
