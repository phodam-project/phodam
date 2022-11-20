<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Primitive;

use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends int
 * @template-implements TypedProviderInterface<int>
 */
class DefaultIntTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContext $context): int
    {
        $config = $context->getConfig();
        $min = $config['min'] ?? -10000;
        $max = $config['max'] ?? 10000;
        return rand($min, $max);
    }
}
