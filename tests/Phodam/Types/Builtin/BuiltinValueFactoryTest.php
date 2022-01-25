<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Tests\Phodam\Types\Builtin;

use Phodam\Types\Builtin\BuiltinValueFactory;
use Phodam\Types\Builtin\Int\IntTypeProviderInterface;
use Tests\Phodam\PhodamTestCase;
use Tests\Phodam\TestObjects\FakeDateTimeTypeProvider;
use Tests\Phodam\TestObjects\FakeIntTypeProvider;

/**
 * @coversDefaultClass \Phodam\Types\Builtin\BuiltinValueFactory
 */
class BuiltinValueFactoryTest extends PhodamTestCase
{
    private BuiltinValueFactory $factory;

    /**
     * @covers ::__construct
     */
    public function setUp(): void
    {
        $this->factory = new BuiltinValueFactory();
    }

    /**
     * @covers ::createInt
     * @covers ::createBuiltinValue
     */
    public function testCreateInt(): void
    {
        $result = $this->factory->createInt();
        $this->assertTrue(is_int($result));
    }

    /**
     * @covers ::createFloat
     * @covers ::createBuiltinValue
     */
    public function testCreateFloat(): void
    {
        $result = $this->factory->createFloat();
        $this->assertTrue(is_float($result));
    }

    /**
     * @covers ::createString
     * @covers ::createBuiltinValue
     */
    public function testCreateString(): void
    {
        $result = $this->factory->createString();
        $this->assertTrue(is_string($result));
    }

    /**
     * @covers ::registerBuiltinTypeProvider
     * @covers ::createBuiltinValue
     */
    public function testRegisterBuiltinTypeProvider(): void
    {
        $customIntTypeProvider = new FakeIntTypeProvider(PHP_INT_MIN);
        $this->factory->registerBuiltinTypeProvider($customIntTypeProvider);

        $actual = $this->factory->createInt();
        $this->assertEquals(PHP_INT_MIN, $actual);
    }

    /**
     * @covers ::__construct
     * @covers ::registerBuiltinTypeProvider
     * @covers ::createBuiltinValue
     */
    public function testRegisterBuiltinTypeProviderWithMock(): void
    {
        $intTypeProviderMock = $this->createMock(IntTypeProviderInterface::class);
        $intTypeProviderMock->expects($this->once())
            ->method('create')
            ->willReturn(PHP_INT_MAX);

        $this->factory->registerBuiltinTypeProvider($intTypeProviderMock);

        $result = $this->factory->createInt();
        $this->assertEquals(PHP_INT_MAX, $result);
    }

    /**
     * @covers ::registerBuiltinTypeProvider
     */
    public function testRegisterBuiltinTypeProviderInvalidType(): void
    {
        $dateTimeTypeProvider = new FakeDateTimeTypeProvider();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Provider is not for a valid builtin class");

        $this->factory->registerBuiltinTypeProvider($dateTimeTypeProvider);
    }
}
