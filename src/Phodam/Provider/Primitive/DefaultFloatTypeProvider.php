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
        $min = $config['min'] ?? -10000;
        $max = $config['max'] ?? 10000;
        $val = rand($min, $max);
        $isNegative = $val <= 0;
        // make sure we're not adding to an already positive max value
        $additive = (rand(0, 100) / 100) * ($isNegative) ? 1 : -1;
        return $val + $additive;
    }
}
