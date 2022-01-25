<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Tests\Phodam\TestObjects;

use Phodam\Types\Builtin\Int\IntTypeProviderInterface;

class FakeIntTypeProvider implements IntTypeProviderInterface
{
    private int $returnValue;

    public function __construct(int $returnValue)
    {
        $this->returnValue = $returnValue;
    }

    public function create(): int
    {
        return $this->returnValue;
    }
}
