<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace Phodam\Provider\Builtin;

use DateTimeImmutable;
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends DateTimeImmutable
 * @template-implements TypedProviderInterface<DateTimeImmutable>
 */
#[PhodamProvider(DateTimeImmutable::class)]
class DefaultDateTimeImmutableTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
