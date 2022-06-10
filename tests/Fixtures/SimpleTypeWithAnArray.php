<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

class SimpleTypeWithAnArray
{
    private $myInt;
    private array $myArray;

    /**
     * @return mixed
     */
    public function getMyInt()
    {
        return $this->myInt;
    }

    /**
     * @param mixed $myInt
     * @return SimpleTypeWithAnArray
     */
    public function setMyInt($myInt)
    {
        $this->myInt = $myInt;
        return $this;
    }

    /**
     * @return array
     */
    public function getMyArray(): array
    {
        return $this->myArray;
    }

    /**
     * @param array $myArray
     * @return SimpleTypeWithAnArray
     */
    public function setMyArray(array $myArray): SimpleTypeWithAnArray
    {
        $this->myArray = $myArray;
        return $this;
    }
}
