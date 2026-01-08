<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Fixtures;

/**
 * This fixture is used to test array type detection.
 * Note: PHP reflection doesn't natively support typed arrays like "string[]",
 * but this class uses PHPDoc comments to document array element types.
 * The TypeAnalyzer checks for types ending with "[]" which would come from
 * alternative type analysis methods (not standard PHP reflection).
 */
class SimpleTypeWithTypedArray
{
    /**
     * @var string[] This is a string array
     */
    private array $stringArray;

    /**
     * @var int[] This is an int array
     */
    private array $intArray;

    /**
     * @return string[]
     */
    public function getStringArray(): array
    {
        return $this->stringArray;
    }

    /**
     * @param string[] $stringArray
     * @return SimpleTypeWithTypedArray
     */
    public function setStringArray(array $stringArray): SimpleTypeWithTypedArray
    {
        $this->stringArray = $stringArray;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getIntArray(): array
    {
        return $this->intArray;
    }

    /**
     * @param int[] $intArray
     * @return SimpleTypeWithTypedArray
     */
    public function setIntArray(array $intArray): SimpleTypeWithTypedArray
    {
        $this->intArray = $intArray;
        return $this;
    }
}
