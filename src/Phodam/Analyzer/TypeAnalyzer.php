<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Analyzer;

use ReflectionNamedType;

class TypeAnalyzer
{
    /**
     * @param string $type
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    public function analyze(string $type): array
    {
        $class = new \ReflectionClass($type);

        $fields = [];
        foreach ($class->getProperties() as $property) {
            /** @var null|ReflectionNamedType $propertyType */
            $propertyType = $property->getType();
            if ($propertyType === null) {
                throw new \Exception('oops');
            }
            $fields[$property->getName()] = [
                'type' => $propertyType->getName(),
                'nullable' => $propertyType->allowsNull(),
                'array' => false
            ];
        }
        return $fields;
    }
}
