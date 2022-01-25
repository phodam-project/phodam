<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Tests\Phodam\Types\Builtin\Int;

use Phodam\Types\Builtin\Int\IntTypeProvider;
use Tests\Phodam\PhodamTestCase;

/**
 * @coversDefaultClass \Phodam\Types\Builtin\Int\IntTypeProvider
 */
class IntTypeProviderTest extends PhodamTestCase
{
    private IntTypeProvider $intTypeProvider;

    public function setUp(): void
    {
        $this->intTypeProvider = new IntTypeProvider();
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $actual = $this->intTypeProvider->create();
        $this->assertTrue(is_int($actual));
    }
}
