<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Tests\Phodam\TestObjects;

use DateTime;
use Phodam\Types\Builtin\BuiltinTypeProvider;

/**
 * @implements BuiltinTypeProvider<DateTime>
 */
class FakeDateTimeTypeProvider implements BuiltinTypeProvider
{
    public function create(): DateTime
    {
        return new DateTime("now");
    }
}
