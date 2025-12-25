<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

/**
 * This fixture is used to test type extraction from PHPDoc @var annotations
 * for properties without type declarations. The TypeAnalyzer should be able
 * to extract types from PHPDoc when properties are untyped.
 */
class SimpleTypeWithPhpDocTypes
{
    /**
     * @var int
     */
    private $myInt;

    /**
     * @var float
     */
    private $myFloat;

    /**
     * @var string
     */
    private $myString;

    /**
     * @var bool
     */
    private $myBool;

    /**
     * @var string[]
     */
    private $stringArray;

    /**
     * @return int
     */
    public function getMyInt()
    {
        return $this->myInt;
    }

    /**
     * @param int $myInt
     * @return SimpleTypeWithPhpDocTypes
     */
    public function setMyInt($myInt): SimpleTypeWithPhpDocTypes
    {
        $this->myInt = $myInt;
        return $this;
    }

    /**
     * @return float
     */
    public function getMyFloat()
    {
        return $this->myFloat;
    }

    /**
     * @param float $myFloat
     * @return SimpleTypeWithPhpDocTypes
     */
    public function setMyFloat($myFloat): SimpleTypeWithPhpDocTypes
    {
        $this->myFloat = $myFloat;
        return $this;
    }

    /**
     * @return string
     */
    public function getMyString()
    {
        return $this->myString;
    }

    /**
     * @param string $myString
     * @return SimpleTypeWithPhpDocTypes
     */
    public function setMyString($myString): SimpleTypeWithPhpDocTypes
    {
        $this->myString = $myString;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMyBool()
    {
        return $this->myBool;
    }

    /**
     * @param bool $myBool
     * @return SimpleTypeWithPhpDocTypes
     */
    public function setMyBool($myBool): SimpleTypeWithPhpDocTypes
    {
        $this->myBool = $myBool;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getStringArray(): array
    {
        return $this->stringArray;
    }

    /**
     * @param string[] $stringArray
     * @return SimpleTypeWithPhpDocTypes
     */
    public function setStringArray(array $stringArray): SimpleTypeWithPhpDocTypes
    {
        $this->stringArray = $stringArray;
        return $this;
    }
}

