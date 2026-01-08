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
use Phodam\Types\TypeDefinition;
use Phodam\Types\FieldDefinition;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class TypeAnalyzer
{
    private ?DocBlockFactoryInterface $docBlockFactory = null;

    /**
     * @param string|class-string<*> $type
     * @return TypeDefinition<*>
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
            $propertyTypeName = null;
            $isArray = false;
            $isNullable = false;

            if ($propertyType === null) {
                // Try to get type from PHPDoc when property has no type declaration
                $typeInfo = $this->getTypeFromPhpDoc($property, $class);
                if ($typeInfo === null) {
                    $unmappedFields[] = $property->getName();
                    continue;
                }

                $propertyTypeName = $typeInfo['type'];
                $isArray = $typeInfo['isArray'];
                // When type comes from PHPDoc only, default to nullable
                $isNullable = true;
            } else {
                $propertyTypeName = $propertyType->getName();
                $isNullable = $propertyType->allowsNull();

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
            }
            $mappedFields[$property->getName()] =
                new FieldDefinition($propertyTypeName, nullable: $isNullable, array: $isArray);
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

        return new TypeDefinition($type, fields: $mappedFields);
    }

    /**
     * Attempts to extract the type from PHPDoc @var annotation
     *
     * @param \ReflectionProperty $property
     * @param \ReflectionClass<*> $context
     * @return array{type: string, isArray: bool}|null The type information or null if not found
     */
    private function getTypeFromPhpDoc(
        \ReflectionProperty $property,
        \ReflectionClass $context
    ): ?array {
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
                $typeString = $this->normalizeTypeString((string)$valueType, $context);

                return [
                    'type' => $typeString,
                    'isArray' => true
                ];
            }

            // Handle non-array types
            $typeString = $this->normalizeTypeString((string)$type, $context);

            return [
                'type' => $typeString,
                'isArray' => false
            ];
        } catch (\Exception $e) {
            // If parsing fails, return null
            return null;
        }
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
        $typeInfo = $this->getTypeFromPhpDoc($property, $context);
        if ($typeInfo === null || !$typeInfo['isArray']) {
            return null;
        }

        return $typeInfo['type'];
    }

    /**
     * Normalizes a type string from PHPDoc, handling fully qualified class names
     * and namespace resolution
     *
     * @param string $typeString
     * @param \ReflectionClass<*> $context
     * @return string The normalized type string
     */
    private function normalizeTypeString(string $typeString, \ReflectionClass $context): string
    {
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

        // Try to resolve the class name in the context's namespace
        $nsGuess = $context->getNamespaceName() . '\\' . $typeString;
        if (class_exists($nsGuess)) {
            return $nsGuess;
        }

        return $typeString;
    }
}
