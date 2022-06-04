<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider\Builtin;

use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Tests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\Primitive\DefaultIntTypeProvider
 */
class DefaultIntTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultIntTypeProvider $provider;

    public function setUp(): void
    {
        $this->provider = new DefaultIntTypeProvider();
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $min = -10000;
        $max = 10000;
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create();
            $this->assertIsInt($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    /**
     * @covers ::create
     */
    public function testCreateWithConfigMinAndMax()
    {
        $min = -100;
        $max = 100;
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], [
                'min' => $min,
                'max' => $max
            ]);
            $this->assertIsInt($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    /**
     * @covers ::create
     */
    public function testCreateWithConfigMinAndMaxWithSmallRange()
    {
        $min = -1;
        $max = 1;
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], [
                'min' => $min,
                'max' => $max
            ]);
            $this->assertIsInt($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }
}
