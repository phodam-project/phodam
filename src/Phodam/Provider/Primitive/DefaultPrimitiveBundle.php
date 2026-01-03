<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Primitive;

use Phodam\Provider\ProviderBundleInterface;
use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use Phodam\Provider\Primitive\DefaultFloatTypeProvider;
use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;

class DefaultPrimitiveBundle implements ProviderBundleInterface
{
    public function getProviders(): array
    {
        return [
            DefaultBoolTypeProvider::class,
            DefaultFloatTypeProvider::class,
            DefaultIntTypeProvider::class,
            DefaultStringTypeProvider::class
        ];
    }

    public function getTypeDefinitions(): array
    {
        return [];
    }
}
