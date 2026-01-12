<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Phodam\Provider\Primitive;

use Phodam\PhodamInterface;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(DefaultStringTypeProvider::class)]
#[CoversMethod(DefaultStringTypeProvider::class, 'create')]
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

    public function testCreate()
    {
        $context = new ProviderContext($this->phodam, 'string', [], []);

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsString($value);
        }
    }

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
