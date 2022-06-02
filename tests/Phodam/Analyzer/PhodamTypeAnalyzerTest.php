<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam;

use Phodam\Analyzer\PhodamTypeAnalyzer;
use Phodam\Phodam;
use Phodam\Tests\Fixtures\SampleProvider;
use Phodam\Tests\Fixtures\SimpleType;

/**
 * @coversDefaultClass \Phodam\Analyzer\PhodamTypeAnalyzer
 */
class PhodamTypeAnalyzerTest extends PhodamTestCase
{
    private PhodamTypeAnalyzer $analyzer;

    public function setUp(): void
    {
        parent::setUp();
        $this->analyzer = new PhodamTypeAnalyzer();
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