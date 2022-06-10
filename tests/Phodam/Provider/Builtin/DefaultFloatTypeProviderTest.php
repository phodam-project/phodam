<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use Phodam\Provider\Primitive\DefaultFloatTypeProvider;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\Primitive\DefaultFloatTypeProvider
 */
class DefaultFloatTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultFloatTypeProvider $provider;

    public function setUp(): void
    {
        $this->provider = new DefaultFloatTypeProvider();
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        // defaults
        $min = -10000;
        $max = 10000;
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create();
            $this->assertIsFloat($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    /**
     * @covers ::create
     */
    public function testCreateWithConfigMinAndMax()
    {
        // defaults
        $min = -100;
        $max = 100;
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], [
                'min' => $min,
                'max' => $max
            ]);
            $this->assertIsFloat($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    /**
     * @covers ::create
     */
    public function testCreateWithConfigMinAndMaxWithSmallRange()
    {
        // defaults
        $min = -1;
        $max = 1;
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], [
                'min' => $min,
                'max' => $max
            ]);
            $this->assertIsFloat($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }
}
