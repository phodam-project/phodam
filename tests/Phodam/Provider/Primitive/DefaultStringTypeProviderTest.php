<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Primitive;

use Phodam\PhodamInterface;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Phodam\Provider\Primitive\DefaultStringTypeProvider
 */
class DefaultStringTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultStringTypeProvider $provider;

    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultStringTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreate()
    {
        $context = new ProviderContext($this->phodam, 'string', [], []);

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsString($value);
        }
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreateWithTypeLower()
    {
        $context = new ProviderContext(
            $this->phodam,
            'string',
            [],
            ['type' => 'lower']
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsString($value);
            $this->assertEquals(strtolower($value), $value);
        }
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreateWithTypeUpper()
    {
        $context = new ProviderContext(
            $this->phodam,
            'string',
            [],
            ['type' => 'upper']
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsString($value);
            $this->assertEquals(strtoupper($value), $value);
        }
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreateWithTypeNumeric()
    {
        $context = new ProviderContext(
            $this->phodam,
            'string',
            [],
            ['type' => 'numeric']
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsString($value);
            for ($j = 0; $j < strlen($value); $j++) {
                $char = $value[$j];
                $this->assertTrue(is_numeric($char));
            }
        }
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreateWithLength()
    {
        $length = 18;
        $minLength = 5;
        $maxLength = 15;

        // 'length' takes precedence over 'minLength' and 'maxLength'
        $context = new ProviderContext(
            $this->phodam,
            'string',
            [],
            [
                'length' => $length,
                'minLength' => $minLength,
                'maxLength' => $maxLength
            ]
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsString($value);
            $this->assertEquals($length, strlen($value));
        }
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreateWithMinAndMaxLength()
    {
        $minLength = 5;
        $maxLength = 15;

        $context = new ProviderContext(
            $this->phodam,
            'string',
            [],
            [
                'minLength' => $minLength,
                'maxLength' => $maxLength
            ]
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsString($value);
            $this->assertGreaterThanOrEqual($minLength, strlen($value));
            $this->assertLessThanOrEqual($maxLength, strlen($value));
        }
    }
}
