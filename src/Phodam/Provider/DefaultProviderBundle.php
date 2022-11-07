<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use Phodam\PhodamSchemaInterface;
use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use Phodam\Provider\Primitive\DefaultFloatTypeProvider;
use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;

class DefaultProviderBundle implements ProviderBundleInterface
{
    public function register(PhodamSchemaInterface $schema): void
    {
        $schema->forType('bool')
            ->registerProvider(DefaultBoolTypeProvider::class);
        $schema->forType('float')
            ->registerProvider(DefaultFloatTypeProvider::class);
        $schema->forType('int')
            ->registerProvider(DefaultIntTypeProvider::class);
        $schema->forType('string')
            ->registerProvider(DefaultStringTypeProvider::class);
    }
}
