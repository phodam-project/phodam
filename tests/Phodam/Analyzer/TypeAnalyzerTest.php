<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Analyzer;

use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Analyzer\TypeAnalyzer;
use Phodam\Tests\Fixtures\SimpleType;
use Phodam\Tests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use Phodam\Tests\Phodam\PhodamBaseTestCase;

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
        $expectedMessage = "Phodam\Tests\Fixtures\SimpleTypeMissingSomeFieldTypes: "
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
}