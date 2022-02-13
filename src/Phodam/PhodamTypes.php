<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

class PhodamTypes
{
    public const PRIMITIVE_FLOAT = "float";
    public const PRIMITIVE_INT = "int";
    public const PRIMITIVE_STRING = "string";

    public const PRIMITIVE_TYPES = [
        self::PRIMITIVE_FLOAT,
        self::PRIMITIVE_INT,
        self::PRIMITIVE_STRING
    ];
}
