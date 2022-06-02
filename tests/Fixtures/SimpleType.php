<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Fixtures;

class SimpleType
{
    private int $myInt;
    private ?float $myFloat;
    private ?string $myString;
    private bool $myBool;

    /**
     * @return int
     */
    public function getMyInt(): int
    {
        return $this->myInt;
    }

    /**
     * @param int $myInt
     */
    public function setMyInt(int $myInt): void
    {
        $this->myInt = $myInt;
    }

    /**
     * @return ?float
     */
    public function getMyFloat(): ?float
    {
        return $this->myFloat;
    }

    /**
     * @param ?float $myFloat
     */
    public function setMyFloat(?float $myFloat): void
    {
        $this->myFloat = $myFloat;
    }

    /**
     * @return ?string
     */
    public function getMyString(): ?string
    {
        return $this->myString;
    }

    /**
     * @param ?string $myString
     */
    public function setMyString(?string $myString): void
    {
        $this->myString = $myString;
    }

    /**
     * @return bool
     */
    public function isMyBool(): bool
    {
        return $this->myBool;
    }

    /**
     * @param bool $myBool
     */
    public function setMyBool(bool $myBool): void
    {
        $this->myBool = $myBool;
    }
}