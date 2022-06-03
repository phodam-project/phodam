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
use Phodam\Tests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Analyzer\TypeAnalysisException
 */
class TypeAnalysisExceptionTest extends PhodamBaseTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getType
     * @covers ::getFieldNames
     * @covers ::getMappedFields
     * @covers ::getUnmappedFields
     */
    public function testConstruct(): void
    {
        $message = 'My Message Here';
        $type = SimpleType::class;
        $fieldNames = [ 'one', 'two', 'three', 'four' ];
        $unmappedFields = [ 'two', 'four' ];
        $mappedFields = [
            'one' => [
                'type' => 'string',
                'nullable' => true,
                'array' => false
            ],
            'three' => [
                'type' => 'int',
                'nullable' => false,
                'array' => false
            ]
        ];

        $ex = new TypeAnalysisException(
            $type,
            $message,
            $fieldNames,
            $mappedFields,
            $unmappedFields
        );

        $this->assertEquals($type, $ex->getType());
        $this->assertEquals($message, $ex->getMessage());
        $this->assertEquals($fieldNames, $ex->getFieldNames());
        $this->assertEquals($mappedFields, $ex->getMappedFields());
        $this->assertEquals($unmappedFields, $ex->getUnmappedFields());
    }
}