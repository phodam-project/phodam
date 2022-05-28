<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider;

use Phodam\Tests\Fixtures\UnregisteredClassType;
use InvalidArgumentException;
use Phodam\PhodamTypes;
use Phodam\Provider\TypeProviderConfig;
use Phodam\Provider\TypeProviderFactory;
use Phodam\Provider\TypeProviderInterface;
use Phodam\Tests\Fixtures\SampleTypeProvider;
use Phodam\Tests\Phodam\PhodamTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\TypeProviderFactory
 */
class TypeProviderFactoryTest extends PhodamTestCase
{
    private TypeProviderInterface $provider;
    private TypeProviderFactory $factory;

    public function setUp(): void
    {
        $this->provider = new SampleTypeProvider();
        $this->factory = new TypeProviderFactory();
    }

    /**
     * @covers ::registerTypeProviderConfig
     */
    public function testRegisterProviderConfigWithoutValidConfig(): void
    {
        // without any configuring, we don't know if it's
        // 1. primitive, 2. array, 3. a class
        $config = new TypeProviderConfig($this->provider);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("A provider config must be declared for an array, primitive, or a class");

        $this->factory->registerTypeProviderConfig($config);
    }

    /**
     * @covers ::getArrayProvider
     * @covers ::registerTypeProviderConfig
     * @covers ::registerArrayTypeProviderConfig
     */
    public function testRegisterProviderConfigForArray(): void
    {
        $name = "MyCoolArray";

        $config = (new TypeProviderConfig($this->provider))
            ->forArray()
            ->withName($name);

        $this->factory->registerTypeProviderConfig($config);

        $result = $this->factory->getArrayProvider($name);

        $this->assertSame($this->provider, $result);
    }

    /**
     * @covers ::registerTypeProviderConfig
     * @covers ::registerArrayTypeProviderConfig
     */
    public function testRegisterProviderConfigForArrayWithNameThatAlreadyExists(): void
    {
        $name = "MyCoolArray";
        $config = (new TypeProviderConfig($this->provider))
            ->forArray()
            ->withName($name);

        $this->factory->registerTypeProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "An array provider with the name MyCoolArray already exists"
        );

        $this->factory->registerTypeProviderConfig($config);
    }

    /**
     * @covers ::getArrayProvider
     */
    public function testGetArrayProviderWithoutRegisteredName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find an array provider with the name MyUnregisteredName");

        $this->factory->getArrayProvider("MyUnregisteredName");
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getPrimitiveProvider
     * @covers ::registerTypeProviderConfig
     * @covers ::registerPrimitiveTypeProviderConfig
     */
    public function testRegisterProviderConfigForPrimitiveWithoutName(): void
    {
        $config = (new TypeProviderConfig($this->provider))
            ->forString();

        $this->factory->registerTypeProviderConfig($config);

        $result = $this->factory->getPrimitiveProvider(PhodamTypes::PRIMITIVE_STRING);

        $this->assertSame($this->provider, $result);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getPrimitiveProvider
     * @covers ::registerTypeProviderConfig
     * @covers ::registerPrimitiveTypeProviderConfig
     */
    public function testRegisterProviderConfigForPrimitiveWithName(): void
    {
        $name = "MyCoolStringProvider";
        $config = (new TypeProviderConfig($this->provider))
            ->forString()
            ->withName($name);

        $this->factory->registerTypeProviderConfig($config);

        $result = $this->factory->getPrimitiveProvider(PhodamTypes::PRIMITIVE_STRING, $name);

        $this->assertSame($this->provider, $result);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getPrimitiveProvider
     * @covers ::registerTypeProviderConfig
     * @covers ::registerPrimitiveTypeProviderConfig
     */
    public function testRegisterProviderConfigForPrimitiveWithNameThatAlreadyExists(): void
    {
        $name = "MyCoolStringProvider";
        $config = (new TypeProviderConfig($this->provider))
            ->forString()
            ->withName($name);

        $this->factory->registerTypeProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "A primitive provider of type string with the name MyCoolStringProvider already exists"
        );

        $this->factory->registerTypeProviderConfig($config);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getPrimitiveProvider
     */
    public function testGetPrimitiveProviderByNameForTypeThatHasNoProviders(): void
    {
        $name = "MyName";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type string with the name MyName");

        $this->factory->getPrimitiveProvider(PhodamTypes::PRIMITIVE_STRING, $name);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getPrimitiveProvider
     */
    public function testGetPrimitiveProviderByNameForTypeThatHasNoNamedProviders(): void
    {
        $name = "MyName";

        $config = (new TypeProviderConfig($this->provider))
            ->forString();
        $this->factory->registerTypeProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type string with the name MyName");

        $this->factory->getPrimitiveProvider(PhodamTypes::PRIMITIVE_STRING, $name);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getPrimitiveProvider
     */
    public function testGetPrimitiveProviderByNameForTypeWithoutRegisteredName(): void
    {
        $name = "MyName";

        $config = (new TypeProviderConfig($this->provider))
            ->forString()
            ->withName("SomeOtherName");

        $this->factory->registerTypeProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type string with the name MyName");

        $this->factory->getPrimitiveProvider(PhodamTypes::PRIMITIVE_STRING, $name);
    }

    /**
     * @covers ::getPrimitiveProvider
     */
    public function testGetPrimitiveProviderForNonPrimitiveType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("someFakePrimitiveClass is not a valid primitive type");

        $this->factory->getPrimitiveProvider("someFakePrimitiveClass");
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getClassProvider
     * @covers ::registerTypeProviderConfig
     * @covers ::registerClassTypeProviderConfig
     */
    public function testRegisterProviderConfigForClassWithoutName(): void
    {
        $class = UnregisteredClassType::class;
        $config = (new TypeProviderConfig($this->provider))
            ->forClass($class);

        $this->factory->registerTypeProviderConfig($config);

        $result = $this->factory->getClassProvider($class);

        $this->assertSame($this->provider, $result);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getClassProvider
     * @covers ::registerTypeProviderConfig
     * @covers ::registerClassTypeProviderConfig
     */
    public function testRegisterProviderConfigForClassWithName(): void
    {
        $class = UnregisteredClassType::class;
        $name = "MyCoolClassProvider";
        $config = (new TypeProviderConfig($this->provider))
            ->forClass($class)
            ->withName($name);

        $this->factory->registerTypeProviderConfig($config);

        $result = $this->factory->getClassProvider($class, $name);

        $this->assertSame($this->provider, $result);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getClassProvider
     * @covers ::registerTypeProviderConfig
     * @covers ::registerClassTypeProviderConfig
     */
    public function testRegisterProviderConfigForClassWithNameThatAlreadyExists(): void
    {
        $class = UnregisteredClassType::class;
        $name = "MyCoolClassProvider";
        $config = (new TypeProviderConfig($this->provider))
            ->forClass($class)
            ->withName($name);

        $this->factory->registerTypeProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "A class provider of type Phodam\Tests\Fixtures\UnregisteredClassType with the name MyCoolClassProvider already exists"
        );

        $this->factory->registerTypeProviderConfig($config);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getClassProvider
     */
    public function testGetClassProviderForTypeThatHasNoDefaultProvider(): void
    {
        $class = UnregisteredClassType::class;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a default provider of type Phodam\Tests\Fixtures\UnregisteredClassType");

        $this->factory->getClassProvider($class);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getClassProvider
     */
    public function testGetClassProviderByNameForTypeThatHasNoProviders(): void
    {
        $class = UnregisteredClassType::class;
        $name = "MyName";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type Phodam\Tests\Fixtures\UnregisteredClassType with the name MyName");

        $this->factory->getClassProvider($class, $name);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getClassProvider
     */
    public function testGetClassProviderByNameForTypeThatHasNoNamedProviders(): void
    {
        $class = UnregisteredClassType::class;
        $name = "MyName";

        $config = (new TypeProviderConfig($this->provider))
            ->forClass($class);
        $this->factory->registerTypeProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type Phodam\Tests\Fixtures\UnregisteredClassType with the name MyName");

        $this->factory->getClassProvider($class, $name);
    }

    /**
     * @covers ::getNamedOrDefaultProvider
     * @covers ::getClassProvider
     */
    public function testGetClassProviderByNameForTypeWithoutRegisteredName(): void
    {
        $class = UnregisteredClassType::class;
        $name = "MyName";

        $config = (new TypeProviderConfig($this->provider))
            ->forString()
            ->withName("SomeOtherName");

        $this->factory->registerTypeProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type Phodam\Tests\Fixtures\UnregisteredClassType with the name MyName");

        $this->factory->getClassProvider($class, $name);
    }
}