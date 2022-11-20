<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Analyzer;

use ReflectionException;
use ReflectionNamedType;

class TypeAnalyzer
{
    /**
     * @param string $type
     * @return TypeDefinition
     * @throws ReflectionException|TypeAnalysisException
     */
    public function analyze(string $type): TypeDefinition
    {
        $class = new \ReflectionClass($type);

        $fieldNames = [];
        $unmappedFields = [];

        $mappedFields = [];
        foreach ($class->getProperties() as $property) {
            $fieldNames[] = $property->getName();

            /** @var null|ReflectionNamedType $propertyType */
            $propertyType = $property->getType();
            if ($propertyType === null) {
                $unmappedFields[] = $property->getName();
                continue;
            }

            // TODO: Can we check if $propertyType === 'SomeClass[]' and treat it as 'SomeClass' and array = true?
            // if this is an array, we can't map the field
            // since we don't know what the type in the array is
            if ($propertyType->getName() === 'array') {
                $unmappedFields[] = $property->getName();
                continue;
            }

            $mappedFields[$property->getName()] = (new FieldDefinition($propertyType->getName()))
                ->setName(null)
                ->setOverrides([])
                ->setConfig([])
                ->setNullable($propertyType->allowsNull())
                ->setArray(false);
        }

        if (!empty($unmappedFields)) {
            throw new TypeAnalysisException(
                $type,
                "$type: Unable to map fields: " . join(', ', $unmappedFields),
                $fieldNames,
                $mappedFields,
                $unmappedFields
            );
        }

        return (new TypeDefinition())
            ->setFields($mappedFields);
    }
}
