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
        $config = $context->getConfig();
        $min = $config['min'] ?? -10000;
        $max = $config['max'] ?? 10000;
        $precision = $config['precision'] ?? 2;
        $val = rand($min, $max);
        $isNegative = $val <= 0;
        $precisionMax = pow(10, $precision);
        // make sure we're not adding to an already positive max value
        $additive = (rand(0, $precisionMax) / $precisionMax) * ($isNegative) ? 1 : -1;
        $val += $additive;
        $val = max($min, $val);
        $val = min($max, $val);
        return round($val, $precision);
    }
}
