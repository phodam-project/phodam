<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Types\Builtin\Float;

class FloatTypeProvider implements FloatTypeProviderInterface
{
    public function create(): float
    {
        return rand(-10000, 10000) / 100.0;
    }
}
