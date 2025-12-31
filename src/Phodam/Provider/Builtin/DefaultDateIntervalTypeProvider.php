<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Builtin;

use DateInterval;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends DateInterval
 * @template-implements TypedProviderInterface<DateInterval>
 */
class DefaultDateIntervalTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): DateInterval
    {
        return new DateInterval('P1D');
    }
}
