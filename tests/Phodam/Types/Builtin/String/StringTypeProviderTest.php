<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Tests\Phodam\Types\Builtin\String;

use Phodam\Types\Builtin\String\StringTypeProvider;
use Tests\Phodam\PhodamTestCase;

/**
 * @coversDefaultClass \Phodam\Types\Builtin\String\StringTypeProvider
 */
class StringTypeProviderTest extends PhodamTestCase
{
    private StringTypeProvider $stringTypeProvider;

    public function setUp(): void
    {
        $this->stringTypeProvider = new StringTypeProvider();
    }

    /**
     * @covers ::create
     * @covers ::getCharInt
     */
    public function testCreate(): void
    {
        $actual = $this->stringTypeProvider->create();
        $this->assertTrue(is_string($actual));
    }
}
