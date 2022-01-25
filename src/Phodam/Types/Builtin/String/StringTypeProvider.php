<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Types\Builtin\String;

class StringTypeProvider implements StringTypeProviderInterface
{
    /**
     * @return string
     */
    public function create(): string
    {
        $str = "";
        for ($i = 0; $i < rand(10, 20); ++$i) {
            $str .= chr($this->getCharInt());
        }
        return $str;
    }

    /**
     * @return int returns an int value of a character
     */
    private function getCharInt(): int
    {
        $charOffset = rand(0, 25);
        // 65-90 => A-Z
        // 97-122 => a-z
        $charStart = (rand(0, 1)) ? 65 : 97;
        return $charOffset + $charStart;
    }
}
