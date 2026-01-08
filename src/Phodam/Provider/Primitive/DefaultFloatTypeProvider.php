<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Provider\Primitive;

use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends float
 * @template-implements TypedProviderInterface<float>
 */
#[PhodamProvider('float')]
class DefaultFloatTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): float
    {
        $config = $context->getConfig();
        $min = $config['min'] ?? -10000;
        $max = $config['max'] ?? 10000;
        $precision = $config['precision'] ?? 2;
        $intMin = (int) floor($min);
        $intMax = (int) ceil($max);
        $val = rand($intMin, $intMax);
        $precisionMax = pow(10, $precision);
        // Add a random fractional part between 0 and 1 (scaled to precision)
        $fractionalPart = rand(0, $precisionMax - 1) / $precisionMax;
        $val += $fractionalPart;
        $val = max($min, $val);
        $val = min($max, $val);
        return round($val, $precision);
    }
}
