<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use DateTime;
use Phodam\Provider\Builtin\DefaultDateTimeTypeProvider;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\Builtin\DefaultDateTimeTypeProvider
 */
class DefaultDateTimeTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultDateTimeTypeProvider $provider;

    public function setUp(): void
    {
        $this->provider = new DefaultDateTimeTypeProvider();
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create();
            $this->assertInstanceOf(DateTime::class, $value);
        }
    }
}
