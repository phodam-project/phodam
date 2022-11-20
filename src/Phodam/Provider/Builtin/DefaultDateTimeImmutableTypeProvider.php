<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Builtin;

use DateTimeImmutable;
use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends DateTimeImmutable
 * @template-implements TypedProviderInterface<DateTimeImmutable>
 */
class DefaultDateTimeImmutableTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContext $context): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
