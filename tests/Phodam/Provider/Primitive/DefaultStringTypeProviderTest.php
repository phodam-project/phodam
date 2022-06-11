<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Primitive;

use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use PhodamTests\Phodam\PhodamBaseTestCase;

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

    /**
     * @covers ::create
     */
    public function testCreateWithDifferentTypes()
    {
        $type = 'lower';
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], ['type' => $type]);
            $this->assertIsString($value);
            $this->assertEquals(strtolower($value), $value);
        }

        $type = 'upper';
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], ['type' => $type]);
            $this->assertIsString($value);
            $this->assertEquals(strtoupper($value), $value);
        }

        $type = 'numeric';
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], ['type' => $type]);
            $this->assertIsString($value);
            for ($j = 0; $j < strlen($value); $j++) {
                $char = $value[$j];
                $this->assertTrue(is_numeric($char));
            }
        }
    }

    /**
     * @covers ::create
     */
    public function testCreateWithLength()
    {
        // 'length' takes precedence over 'minLength' and 'maxLength'
        $length = 18;
        $minLength = 5;
        $maxLength = 15;
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], [
                'length' => $length,
                'minLength' => $minLength,
                'maxLength' => $maxLength
            ]);
            $this->assertIsString($value);
            $this->assertEquals($length, strlen($value));
        }
    }

    /**
     * @covers ::create
     */
    public function testCreateWithMinAndMaxLength()
    {
        $minLength = 5;
        $maxLength = 15;
        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create([], [
                'minLength' => $minLength,
                'maxLength' => $maxLength
            ]);
            $this->assertIsString($value);
            $this->assertGreaterThanOrEqual($minLength, strlen($value));
            $this->assertLessThanOrEqual($maxLength, strlen($value));
        }
    }
}
