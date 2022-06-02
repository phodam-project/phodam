<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

/**
 * @template T
 */
interface ProviderInterface
{
    /**
     * @param array<string, mixed> $overrides values to override
     * @param array<string, mixed> $config provider-specific information. an
     *     open-ended array for the provider to pass information along
     * @return T
     */
    public function create(array $overrides = [], array $config = []);
}
