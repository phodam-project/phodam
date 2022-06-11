<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider\Builtin;

use DateTime;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends DateTime
 * @template-implements TypedProviderInterface<DateTime>
 */
class DefaultDateTimeTypeProvider implements TypedProviderInterface
{
    public function create(array $overrides = [], array $config = []): DateTime
    {
        return new DateTime();
    }
}
