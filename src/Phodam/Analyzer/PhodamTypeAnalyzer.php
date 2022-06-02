<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Analyzer;

class PhodamTypeAnalyzer
{
    public function analyze(string $type): array
    {
        $class = new \ReflectionClass($type);

        $fields = [];
        foreach ($class->getProperties() as $property) {
            $fields[$property->getName()] = $property->getType();
        }
        return $fields;
    }
}
