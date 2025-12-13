<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Builtin;

use DateTimeZone;
use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends DateTimeZone
 * @template-implements TypedProviderInterface<DateTimeZone>
 */
class DefaultDateTimeZoneTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContext $context): DateTimeZone
    {
        return new DateTimeZone('UTC');
    }
}
