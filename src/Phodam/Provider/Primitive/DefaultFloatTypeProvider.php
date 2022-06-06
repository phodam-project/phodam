<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Primitive;

use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends float
 * @template-implements TypedProviderInterface<float>
 */
class DefaultFloatTypeProvider implements TypedProviderInterface
{
    public function create(array $overrides = [], array $config = []): float
    {
        $min = $config['min'] ?? -10000.0;
        $max = $config['max'] ?? 10000.0;
        $precision = $config['precision'] ?? 4;

        $additive = lcg_value() * abs($max - $min);
        return round($min + $additive, $precision);
    }
}
