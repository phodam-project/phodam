<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Analyzer;

use Phodam\Analyzer\TypeAnalysisException;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(\Phodam\Analyzer\TypeAnalysisException::class)]
#[CoversMethod(\Phodam\Analyzer\TypeAnalysisException::class, '__construct')]
#[CoversMethod(\Phodam\Analyzer\TypeAnalysisException::class, 'getType')]
#[CoversMethod(\Phodam\Analyzer\TypeAnalysisException::class, 'getFieldNames')]
#[CoversMethod(\Phodam\Analyzer\TypeAnalysisException::class, 'getMappedFields')]
#[CoversMethod(\Phodam\Analyzer\TypeAnalysisException::class, 'getUnmappedFields')]
class TypeAnalysisExceptionTest extends PhodamBaseTestCase
{
    public function testConstruct(): void
    {
        $message = 'My Message Here';
        $type = SimpleType::class;
        $fieldNames = ['one', 'two', 'three', 'four'];
        $unmappedFields = ['two', 'four'];
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
