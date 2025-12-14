<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Analyzer;

use Exception;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Analyzer\TypeAnalyzer;
use Phodam\Analyzer\TypeDefinition;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\SimpleTypeWithAnArray;
use PhodamTests\Fixtures\SimpleTypeWithTypedArray;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Analyzer\TypeAnalyzer
 */
class TypeAnalyzerTest extends PhodamBaseTestCase
{
    private TypeAnalyzer $analyzer;

    public function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new TypeAnalyzer();
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyze(): void
    {
        $expected = [
            'myInt' => (new FieldDefinition('int')),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => (new FieldDefinition('bool'))
        ];

        $result = $this->analyzer->analyze(SimpleType::class);
        $this->assertInstanceOf(TypeDefinition::class, $result);
        $this->assertEquals($expected, $result->getFields());

        // Verify isArray is false for all fields
        foreach ($result->getFields() as $field) {
            $this->assertFalse($field->isArray(), "Field {$field->getType()} should have isArray() = false");
        }
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeWithUnmappedFields(): void
    {
        $expectedMessage = "PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes: "
            . "Unable to map fields: myInt, myString";
        $expectedFieldNames = [
            'myInt', 'myFloat', 'myString', 'myBool'
        ];
        $expectedUnmappedFields = [
            'myInt', 'myString'
        ];
        $expectedMappedFields = [
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myBool' => (new FieldDefinition('bool'))
        ];

        try {
            $result = $this->analyzer->analyze(SimpleTypeMissingSomeFieldTypes::class);
        } catch (Exception $ex) {
            $this->assertInstanceOf(TypeAnalysisException::class, $ex);
            $this->assertEquals(SimpleTypeMissingSomeFieldTypes::class, $ex->getType());
            $this->assertEquals($expectedMessage, $ex->getMessage());
            $this->assertEquals($expectedFieldNames, $ex->getFieldNames());
            $this->assertEquals($expectedUnmappedFields, $ex->getUnmappedFields());
            $this->assertEquals($expectedMappedFields, $ex->getMappedFields());
        }
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzeWithAnArray(): void
    {
        $expectedMessage = "PhodamTests\Fixtures\SimpleTypeWithAnArray: "
            . "Unable to map fields: myInt, myArray";
        $expectedFieldNames = [
            'myInt', 'myArray'
        ];
        $expectedUnmappedFields = [
            'myInt', 'myArray'
        ];
        $expectedMappedFields = [];

        try {
            $this->analyzer->analyze(SimpleTypeWithAnArray::class);
        } catch (Exception $ex) {
            $this->assertInstanceOf(TypeAnalysisException::class, $ex);
            $this->assertEquals(SimpleTypeWithAnArray::class, $ex->getType());
            $this->assertEquals($expectedMessage, $ex->getMessage());
            $this->assertEquals($expectedFieldNames, $ex->getFieldNames());
            $this->assertEquals($expectedUnmappedFields, $ex->getUnmappedFields());
            $this->assertEquals($expectedMappedFields, $ex->getMappedFields());
        }
    }

    /**
     * @covers ::analyze
     * 
     * This test verifies that the isArray flag is correctly set to false
     * for non-array types. The TypeAnalyzer contains logic to detect types
     * ending with '[]' and set isArray to true, but standard PHP reflection
     * doesn't return types in this format. This test ensures the default
     * behavior (isArray = false) works correctly.
     */
    public function testIsArrayIsFalseForNonArrayTypes(): void
    {
        $result = $this->analyzer->analyze(SimpleType::class);
        $fields = $result->getFields();

        // All fields in SimpleType are scalar types, so isArray should be false
        foreach ($fields as $fieldName => $field) {
            $this->assertFalse(
                $field->isArray(),
                "Field '{$fieldName}' of type '{$field->getType()}' should have isArray() = false"
            );
        }
    }

    /**
     * @covers ::analyze
     * @covers ::getArrayElementTypeFromPhpDoc
     */
    public function testAnalyzeWithTypedArraysFromPhpDoc(): void
    {
        $result = $this->analyzer->analyze(SimpleTypeWithTypedArray::class);
        $fields = $result->getFields();

        // Verify that array fields with PHPDoc types are properly mapped
        $this->assertArrayHasKey('stringArray', $fields);
        $stringArrayField = $fields['stringArray'];
        $this->assertEquals('string', $stringArrayField->getType());
        $this->assertTrue($stringArrayField->isArray(), 'stringArray should have isArray() = true');

        $this->assertArrayHasKey('intArray', $fields);
        $intArrayField = $fields['intArray'];
        $this->assertEquals('int', $intArrayField->getType());
        $this->assertTrue($intArrayField->isArray(), 'intArray should have isArray() = true');
    }
}
