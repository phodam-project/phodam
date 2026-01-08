<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Types;

class TypeNormalizer
{
    public static function normalize(string $type): string
    {
        // Normalize types to match the names returned by gettype().
        switch ($type) {
            case 'bool':
                return 'boolean';

            case 'int':
                return 'integer';

            case 'float':
                return 'double';

            default:
                return $type;
        }
    }
}
