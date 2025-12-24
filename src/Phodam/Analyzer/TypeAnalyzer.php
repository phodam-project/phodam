<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Analyzer;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Types\Array_;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class TypeAnalyzer
{
    private ?DocBlockFactoryInterface $docBlockFactory = null;

    /**
     * @param string $type
     * @return TypeDefinition
     * @throws ReflectionException|TypeAnalysisException
     */
    public function analyze(string $type): TypeDefinition
    {
        $class = new ReflectionClass($type);

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

            $propertyTypeName = $propertyType->getName();
            $isArray = false;

            // Check if this is an array type and try to get element type from PHPDoc
            if ($propertyType->getName() === 'array') {
                $elementType = $this->getArrayElementTypeFromPhpDoc($property, $class);
                if ($elementType === null) {
                    $unmappedFields[] = $property->getName();
                    continue;
                }

                $propertyTypeName = $elementType;
                $isArray = true;
            }

            $mappedFields[$property->getName()] = (new FieldDefinition($propertyTypeName))
                ->setName(null)
                ->setOverrides([])
                ->setConfig([])
                ->setNullable($propertyType->allowsNull())
                ->setArray($isArray);
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

    /**
     * Attempts to extract the array element type from PHPDoc @var annotation
     *
     * @param \ReflectionProperty $property
     * @param \ReflectionClass<*> $context
     * @return string|null The element type (e.g., 'string', 'int', 'SomeClass') or null if not found
     */
    private function getArrayElementTypeFromPhpDoc(
        \ReflectionProperty $property,
        \ReflectionClass $context
    ): ?string {
        $docComment = $property->getDocComment();
        if ($docComment === false) {
            return null;
        }

        if ($this->docBlockFactory === null) {
            $this->docBlockFactory = DocBlockFactory::createInstance();
        }

        try {
            $docblock = $this->docBlockFactory->create($docComment);
            $varTags = $docblock->getTagsByName('var');

            if (empty($varTags)) {
                return null;
            }

            /** @var Var_ $varTag */
            $varTag = $varTags[0];
            $type = $varTag->getType();

            // Check if the type is an array type
            if ($type instanceof Array_) {
                $valueType = $type->getValueType();
                // Convert the type object to string (e.g., Object_ -> class name, String_ -> 'string')
                $typeString = (string)$valueType;

                // Handle fully qualified class names - phpDocumentor returns them with backslashes
                // We want just the class name for consistency with reflection
                if (strpos($typeString, '\\') !== false) {
                    // Extract the class name from the fully qualified name
                    $parts = explode('\\', $typeString);
                    $typeString = end($parts);
                }

                if ($typeString[0] === '\\') {
                    return ltrim($typeString, '\\');
                }

                $nsGuess = $context->getnamespaceName() . '\\' . $typeString;
                if (class_exists($nsGuess)) {
                    return $nsGuess;
                }

                return $typeString;
            }
        } catch (\Exception $e) {
            // If parsing fails, return null
            return null;
        }

        return null;
    }
}
