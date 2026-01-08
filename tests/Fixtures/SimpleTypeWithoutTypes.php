<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Fixtures;

class SimpleTypeWithoutTypes
{
    private $myInt;
    private $myFloat;
    private $myString;
    private $myBool;

    public function getMyInt()
    {
        return $this->myInt;
    }

    public function setMyInt($myInt)
    {
        $this->myInt = $myInt;
    }

    public function getMyFloat()
    {
        return $this->myFloat;
    }

    public function setMyFloat($myFloat)
    {
        $this->myFloat = $myFloat;
    }

    public function getMyString()
    {
        return $this->myString;
    }

    public function setMyString($myString)
    {
        $this->myString = $myString;
    }

    public function isMyBool()
    {
        return $this->myBool;
    }

    public function setMyBool($myBool)
    {
        $this->myBool = $myBool;
    }
}
