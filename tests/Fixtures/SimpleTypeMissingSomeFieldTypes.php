<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

class SimpleTypeMissingSomeFieldTypes
{
    private $myInt;
    private ?float $myFloat;
    private $myString;
    private bool $myBool;

    public function getMyInt()
    {
        return $this->myInt;
    }

    /**
     * @param $myInt
     */
    public function setMyInt($myInt): void
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

    public function getMyString()
    {
        return $this->myString;
    }

    public function setMyString($myString): void
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