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
 * @template T extends float
 * @template-implements TypedProviderInterface<float>
 */
class DefaultFloatTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContext $context): float
    {
<<<<<<< HEAD
        $min = $config['min'] ?? -10000.0;
        $max = $config['max'] ?? 10000.0;
        $precision = $config['precision'] ?? 4;

        $additive = lcg_value() * abs($max - $min);
        return round($min + $additive, $precision);
=======
        $config = $context->getConfig();
        $min = $config['min'] ?? -10000;
        $max = $config['max'] ?? 10000;
        $val = rand($min, $max);
        $isNegative = $val <= 0;
        // make sure we're not adding to an already positive max value
        $additive = (rand(0, 100) / 100) * ($isNegative) ? 1 : -1;
        return $val + $additive;
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
    }
}
