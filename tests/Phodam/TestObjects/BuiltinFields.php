<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Tests\Phodam\TestObjects;

class BuiltinFields
{
    private int $myInt;
    private float $myFloat;
    private string $myString;

    public function getInt(): int
    {
        return $this->myInt;
    }

    public function getFloat(): float
    {
        return $this->myFloat;
    }

    public function getString(): string
    {
        return $this->myString;
    }
}