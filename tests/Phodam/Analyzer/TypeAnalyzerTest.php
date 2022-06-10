<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Analyzer;

use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Analyzer\TypeAnalyzer;
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
            'myInt' => [
                'type' => 'int',
                'name' => null,
                'overrides' => [],
                'config' => [],
                'nullable' => false,
                'array' => false
            ],
            'myFloat' => [
                'type' => 'float',
                'name' => null,
                'overrides' => [],
                'config' => [],
                'nullable' => true,
                'array' => false
            ],
            'myString' => [
                'type' => 'string',
                'name' => null,
                'overrides' => [],
                'config' => [],
                'nullable' => true,
                'array' => false
            ],
            'myBool' => [
                'type' => 'bool',
                'name' => null,
                'overrides' => [],
                'config' => [],
                'nullable' => false,
                'array' => false
            ]
        ];

        $result = $this->analyzer->analyze(SimpleType::class);
        $this->assertEquals($expected, $result);
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
            'myFloat' => [
                'type' => 'float',
                'name' => null,
                'overrides' => [],
                'config' => [],
                'nullable' => true,
                'array' => false
            ],
            'myBool' => [
                'type' => 'bool',
                'name' => null,
                'overrides' => [],
                'config' => [],
                'nullable' => false,
                'array' => false
            ]
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