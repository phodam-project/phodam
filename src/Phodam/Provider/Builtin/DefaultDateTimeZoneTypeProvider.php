<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Provider\Builtin;

use DateTimeZone;
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends DateTimeZone
 * @template-implements TypedProviderInterface<DateTimeZone>
 */
#[PhodamProvider(DateTimeZone::class)]
class DefaultDateTimeZoneTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): DateTimeZone
    {
        return new DateTimeZone('UTC');
    }
}
