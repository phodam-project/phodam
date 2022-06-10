<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\Primitive\DefaultBoolTypeProvider
 */
class DefaultBoolTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultBoolTypeProvider $provider;

    public function setUp(): void
    {
        $this->provider = new DefaultBoolTypeProvider();
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create();
            $this->assertIsBool($value);
        }
    }
}
