<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Phodam\PhodamSchemaInterface;
use Phodam\Provider\Builtin\DefaultDateIntervalTypeProvider;
use Phodam\Provider\Builtin\DefaultDatePeriodTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeZoneTypeProvider;
use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use Phodam\Provider\Primitive\DefaultFloatTypeProvider;
use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;

class DefaultProviderBundle implements ProviderBundleInterface
{
    public function register(PhodamSchemaInterface $schema): void
    {
        // primitive types
        $schema->forType('bool')
            ->registerProvider(DefaultBoolTypeProvider::class);
        $schema->forType('float')
            ->registerProvider(DefaultFloatTypeProvider::class);
        $schema->forType('int')
            ->registerProvider(DefaultIntTypeProvider::class);
        $schema->forType('string')
            ->registerProvider(DefaultStringTypeProvider::class);

        // builtin types
        $schema->forType(DateInterval::class)
            ->registerProvider(DefaultDateIntervalTypeProvider::class);
        $schema->forType(DatePeriod::class)
            ->registerProvider(DefaultDatePeriodTypeProvider::class);
        $schema->forType(DateTime::class)
            ->registerProvider(DefaultDateTimeTypeProvider::class);
        $schema->forType(DateTimeImmutable::class)
            ->registerProvider(DefaultDateTimeImmutableTypeProvider::class);
        $schema->forType(DateTimeZone::class)
            ->registerProvider(DefaultDateTimeZoneTypeProvider::class);
    }
}
