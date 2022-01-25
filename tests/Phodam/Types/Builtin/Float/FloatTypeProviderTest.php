<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Tests\Phodam\Types\Builtin\Float;

use Phodam\Types\Builtin\Float\FloatTypeProvider;
use Tests\Phodam\PhodamTestCase;

/**
 * @coversDefaultClass \Phodam\Types\Builtin\Float\FloatTypeProvider
 */
class FloatTypeProviderTest extends PhodamTestCase
{
    private FloatTypeProvider $floatTypeProvider;

    public function setUp(): void
    {
        $this->floatTypeProvider = new FloatTypeProvider();
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $actual = $this->floatTypeProvider->create();
        $this->assertTrue(is_float($actual));
    }
}
