<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

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
     * @return SimpleType
     */
    public function setMyInt(int $myInt): SimpleType
    {
        $this->myInt = $myInt;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMyFloat(): ?float
    {
        return $this->myFloat;
    }

    /**
     * @param float|null $myFloat
     * @return SimpleType
     */
    public function setMyFloat(?float $myFloat): SimpleType
    {
        $this->myFloat = $myFloat;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMyString(): ?string
    {
        return $this->myString;
    }

    /**
     * @param string|null $myString
     * @return SimpleType
     */
    public function setMyString(?string $myString): SimpleType
    {
        $this->myString = $myString;
        return $this;
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
     * @return SimpleType
     */
    public function setMyBool(bool $myBool): SimpleType
    {
        $this->myBool = $myBool;
        return $this;
    }
}
