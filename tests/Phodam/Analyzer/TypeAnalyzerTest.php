<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam;

use Phodam\Analyzer\TypeAnalyzer;
use Phodam\Phodam;
use Phodam\Tests\Fixtures\SampleProvider;
use Phodam\Tests\Fixtures\SimpleType;

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
            'myInt' => 'int',
            'myFloat' => 'float',
            'myString' => 'string'
        ];

        $result = $this->analyzer->analyze(SimpleType::class);
        $this->assertEquals($expected, $result);
    }
}