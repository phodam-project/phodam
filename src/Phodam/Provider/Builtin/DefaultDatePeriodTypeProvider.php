<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Provider\Builtin;

use DateInterval;
use DatePeriod;
use DateTime;
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends DatePeriod
 * @template-implements TypedProviderInterface<DatePeriod>
 */
#[PhodamProvider(DatePeriod::class)]
class DefaultDatePeriodTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): DatePeriod
    {
        $start = new DateTime();
        $interval = new DateInterval('P1D');
        $end = new DateTime('+7 days');

        return new DatePeriod($start, $interval, $end);
    }
}
