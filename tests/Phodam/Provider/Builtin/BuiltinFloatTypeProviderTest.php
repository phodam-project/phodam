<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider\Builtin;

use Phodam\Provider\Builtin\BuiltinFloatTypeProvider;
use Phodam\Tests\Phodam\PhodamTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\Builtin\BuiltinFloatTypeProvider
 */
class BuiltinFloatTypeProviderTest extends PhodamTestCase
{
    private BuiltinFloatTypeProvider $provider;

    public function setUp(): void
    {
        $this->provider = new BuiltinFloatTypeProvider();
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create();
            $this->assertIsFloat($value);
        }
    }
}
