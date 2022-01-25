<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Tests\Phodam;

use Phodam\PhodamFactory;
use Phodam\Types\Builtin\BuiltinValueFactory;
use Tests\Phodam\TestObjects\BuiltinFields;
use Tests\Phodam\TestObjects\FakeIntTypeProvider;

/**
 * @coversDefaultClass \Phodam\PhodamFactory
 */
class PhodamFactoryTest extends PhodamTestCase
{
    private PhodamFactory $phodamFactory;

    public function setUp(): void
    {
        $this->phodamFactory = new PhodamFactory();
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $result = $this->phodamFactory->create(BuiltinFields::class);
        $this->assertNull($result);
    }

    /**
     * @covers ::createInt
     */
    public function testCreateInt(): void
    {
        $intValue = $this->phodamFactory->createInt();
        $this->assertTrue(is_int($intValue));
    }

    /**
     * @covers ::createFloat
     */
    public function testCreateFloat(): void
    {
        $floatValue = $this->phodamFactory->createFloat();
        $this->assertTrue(is_float($floatValue));
    }

    /**
     * @covers ::createString
     */
    public function testCreateString(): void
    {
        $strValue = $this->phodamFactory->createString();
        $this->assertTrue(is_string($strValue));
    }
    /**
     * @covers ::__construct
     * @covers ::registerBuiltinTypeProvider
     */
    public function testRegisterBuiltinTypeProvider(): void
    {
        $customIntTypeProvider = new FakeIntTypeProvider(PHP_INT_MIN);
        $builtinValueFactory =
            $this->createMock(BuiltinValueFactory::class);
        $localPhodamFactory = new PhodamFactory($builtinValueFactory);

        $builtinValueFactory->expects($this->once())
            ->method("registerBuiltinTypeProvider")
            ->with($this->equalTo($customIntTypeProvider));

        $localPhodamFactory->registerBuiltinTypeProvider($customIntTypeProvider);
    }
}
