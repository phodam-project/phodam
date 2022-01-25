<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Types\Builtin\Int;

class IntTypeProvider implements IntTypeProviderInterface
{
    /**
     * @return int
     */
    public function create(): int
    {
        return rand(-10000, 10000);
    }
}
