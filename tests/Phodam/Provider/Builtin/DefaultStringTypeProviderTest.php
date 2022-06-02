<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider\Builtin;

use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Tests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\Primitive\DefaultStringTypeProvider
 */
class DefaultStringTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultStringTypeProvider $provider;

    public function setUp(): void
    {
        $this->provider = new DefaultStringTypeProvider();
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create();
            $this->assertIsString($value);
        }
    }
}
