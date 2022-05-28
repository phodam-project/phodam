<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider;

use DateTime;
use InvalidArgumentException;
use Phodam\PhodamTypes;
use Phodam\Provider\TypeProviderConfig;
use Phodam\Provider\TypeProviderInterface;
use Phodam\Tests\Fixtures\SampleTypeProvider;
use Phodam\Tests\Phodam\PhodamTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\TypeProviderConfig
 */
class TypeProviderConfigTest extends PhodamTestCase
{
    private TypeProviderInterface $provider;

    public function setUp(): void
    {
        $this->provider = new SampleTypeProvider();
    }

    /**
     * @covers ::__construct
     * @covers ::getTypeProvider
     */
    public function testConstruct()
    {
        $config = new TypeProviderConfig($this->provider);
        $this->assertEquals($this->provider, $config->getTypeProvider());
    }

    /**
     * @covers ::forArray
     * @covers ::withName
     * @covers ::isArray
     * @covers ::getClass
     * @covers ::getName
     * @covers ::getPrimitive
     * @covers ::getTypeProvider
     */
    public function testForArray()
    {
        $name = "MyArrayName";
        $config = (new TypeProviderConfig($this->provider))
            ->forArray()
            ->withName($name);

        $this->assertTrue($config->isArray());
        $this->assertNull($config->getClass());
        $this->assertNull($config->getPrimitive());
        $this->assertEquals($name, $config->getName());
        $this->assertEquals($this->provider, $config->getTypeProvider());
    }

    /**
     * @covers ::forClass
     * @covers ::withName
     * @covers ::isArray
     * @covers ::getClass
     * @covers ::getName
     * @covers ::getPrimitive
     * @covers ::getTypeProvider
     */
    public function testForClass()
    {
        $name = "MyDateTimeName";
        $config = (new TypeProviderConfig($this->provider))
            ->forClass(DateTime::class)
            ->withName($name);

        $this->assertFalse($config->isArray());
        $this->assertEquals(DateTime::class, $config->getClass());
        $this->assertNull($config->getPrimitive());
        $this->assertEquals($name, $config->getName());
        $this->assertEquals($this->provider, $config->getTypeProvider());
    }

    /**
     * @covers ::forFloat
     * @covers ::setPrimitive
     * @covers ::isArray
     * @covers ::getClass
     * @covers ::getName
     * @covers ::getPrimitive
     * @covers ::getTypeProvider
     */
    public function testForFloat()
    {
        $config = (new TypeProviderConfig($this->provider))
            ->forFloat();

        $this->assertFalse($config->isArray());
        $this->assertNull($config->getClass());
        $this->assertEquals(PhodamTypes::PRIMITIVE_FLOAT, $config->getPrimitive());
        $this->assertNull($config->getName());
        $this->assertEquals($this->provider, $config->getTypeProvider());
    }

    /**
     * @covers ::forInt
     * @covers ::setPrimitive
     * @covers ::withName
     * @covers ::isArray
     * @covers ::getClass
     * @covers ::getName
     * @covers ::getPrimitive
     * @covers ::getTypeProvider
     */
    public function testForInt()
    {
        $name = "MyIntName";
        $config = (new TypeProviderConfig($this->provider))
            ->forInt()
            ->withName($name);

        $this->assertFalse($config->isArray());
        $this->assertNull($config->getClass());
        $this->assertEquals(PhodamTypes::PRIMITIVE_INT, $config->getPrimitive());
        $this->assertEquals($name, $config->getName());
        $this->assertEquals($this->provider, $config->getTypeProvider());
    }

    /**
     * @covers ::forString
     * @covers ::setPrimitive
     * @covers ::isArray
     * @covers ::getClass
     * @covers ::getName
     * @covers ::getPrimitive
     * @covers ::getTypeProvider
     */
    public function testForString()
    {
        $config = (new TypeProviderConfig($this->provider))
            ->forString();

        $this->assertFalse($config->isArray());
        $this->assertNull($config->getClass());
        $this->assertEquals(PhodamTypes::PRIMITIVE_STRING, $config->getPrimitive());
        $this->assertNull($config->getName());
        $this->assertEquals($this->provider, $config->getTypeProvider());
    }

    /**
     * @covers ::validate
     */
    public function testValidate(): void
    {
        $config = (new TypeProviderConfig($this->provider))
            ->forString();

        $config->validate();

        $this->assertEquals(PhodamTypes::PRIMITIVE_STRING, $config->getPrimitive());
    }

    /**
     * @covers ::validate
     */
    public function testValidateWithArrayWithoutName(): void
    {
        $config = (new TypeProviderConfig($this->provider))
            ->forArray();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("An array provider config must have a name");

        $config->validate();
    }

    /**
     * @covers ::validate
     */
    public function testValidateWithoutAnyValidType(): void
    {
        $config = (new TypeProviderConfig($this->provider));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("A provider config must be declared for an array, primitive, or a class");

        $config->validate();
    }
}
