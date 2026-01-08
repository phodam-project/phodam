<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Provider\Builtin;

use Phodam\Provider\Builtin\DefaultDateIntervalTypeProvider;
use Phodam\Provider\Builtin\DefaultDatePeriodTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeZoneTypeProvider;
use Phodam\Provider\ProviderBundleInterface;

class DefaultBuiltinBundle implements ProviderBundleInterface
{
    public function getProviders(): array
    {
        return [
            DefaultDateIntervalTypeProvider::class,
            DefaultDatePeriodTypeProvider::class,
            DefaultDateTimeTypeProvider::class,
            DefaultDateTimeImmutableTypeProvider::class,
            DefaultDateTimeZoneTypeProvider::class
        ];
    }

    public function getTypeDefinitions(): array
    {
        return [];
    }
}
