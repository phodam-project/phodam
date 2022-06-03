<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Primitive;

use Phodam\Provider\ProviderInterface;

/**
 * @template T extends string
 * @template-implements ProviderInterface<string>
 */
class DefaultStringTypeProvider implements ProviderInterface
{
    public function create(array $overrides = [], array $config = []): string
    {
        return bin2hex(random_bytes(rand(16, 32)));
    }
}
