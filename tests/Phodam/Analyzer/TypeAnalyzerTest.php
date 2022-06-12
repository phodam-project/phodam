<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Analyzer;

use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Analyzer\TypeAnalyzer;
use Phodam\Analyzer\TypeDefinition;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\SimpleTypeWithAnArray;
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
        } catch (\Exception $ex) {
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
        } catch (\Exception $ex) {
            $this->assertInstanceOf(TypeAnalysisException::class, $ex);
            $this->assertEquals(SimpleTypeWithAnArray::class, $ex->getType());
            $this->assertEquals($expectedMessage, $ex->getMessage());
            $this->assertEquals($expectedFieldNames, $ex->getFieldNames());
            $this->assertEquals($expectedUnmappedFields, $ex->getUnmappedFields());
            $this->assertEquals($expectedMappedFields, $ex->getMappedFields());
        }
    }
}