<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam;

use InvalidArgumentException;
use Phodam\Phodam;
use Phodam\Provider\ProviderConfig;
use Phodam\Provider\ProviderNotFoundException;
use Phodam\Tests\Fixtures\SampleArrayProvider;
use Phodam\Tests\Fixtures\SampleProvider;
use Phodam\Tests\Fixtures\SimpleType;
use Phodam\Tests\Fixtures\UnregisteredClassType;

/**
 * @coversDefaultClass \Phodam\Phodam
 * @covers ::__construct
 * @covers ::registerPrimitiveTypeProviders
 */
class PhodamTest extends PhodamBaseTestCase
{
    private Phodam $phodam;
    private SampleProvider $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->phodam = new Phodam();
        $this->provider = new SampleProvider();
    }

    /**
     * @covers ::registerProviderConfig
     */
    public function testRegisterProviderConfigWithoutValidConfig(): void
    {
        // without any configuring, we don't know if it's
        // an array or a type
        $config = new ProviderConfig($this->provider);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("A provider config must be declared for an array or a type");

        $this->phodam->registerProviderConfig($config);
    }

    /**
     * @covers ::getArrayProvider
     * @covers ::registerProviderConfig
     * @covers ::registerArrayProviderConfig
     */
    public function testRegisterProviderConfigForArray(): void
    {
        $name = "MyCoolArray";

        $config = (new ProviderConfig($this->provider))
            ->forArray()
            ->withName($name);

        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->getArrayProvider($name);

        $this->assertSame($this->provider, $result);
    }

    /**
     * @covers ::registerTypeProviderConfig
     * @covers ::registerArrayProviderConfig
     */
    public function testRegisterProviderConfigForArrayWithNameThatAlreadyExists(): void
    {
        $name = "MyCoolArray";
        $config = (new ProviderConfig($this->provider))
            ->forArray()
            ->withName($name);

        $this->phodam->registerProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "An array provider with the name MyCoolArray already exists"
        );

        $this->phodam->registerProviderConfig($config);
    }

    /**
     * @covers ::getArrayProvider
     */
    public function testGetArrayProviderWithoutRegisteredName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find an array provider with the name MyUnregisteredName");

        $this->phodam->getArrayProvider("MyUnregisteredName");
    }

    /**
     * @covers ::getTypeProvider
     * @covers ::registerProviderConfig
     * @covers ::registerTypeProviderConfig
     */
    public function testRegisterProviderConfigForClassWithoutName(): void
    {
        $type = UnregisteredClassType::class;
        $config = (new ProviderConfig($this->provider))
            ->forType($type);

        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->getTypeProvider($type);

        $this->assertSame($this->provider, $result);
    }

    /**
     * @covers ::getTypeProvider
     * @covers ::registerProviderConfig
     * @covers ::registerTypeProviderConfig
     */
    public function testRegisterProviderConfigForClassWithName(): void
    {
        $type = UnregisteredClassType::class;
        $name = "MyCoolClassProvider";
        $config = (new ProviderConfig($this->provider))
            ->forType($type)
            ->withName($name);

        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->getTypeProvider($type, $name);

        $this->assertSame($this->provider, $result);
    }

    /**
     * @covers ::getTypeProvider
     * @covers ::registerProviderConfig
     * @covers ::registerTypeProviderConfig
     */
    public function testRegisterProviderConfigForTypeWithNameThatAlreadyExists(): void
    {
        $type = UnregisteredClassType::class;
        $name = "MyCoolClassProvider";
        $config = (new ProviderConfig($this->provider))
            ->forType($type)
            ->withName($name);

        $this->phodam->registerProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "A type provider of type Phodam\Tests\Fixtures\UnregisteredClassType with the name MyCoolClassProvider already exists"
        );

        $this->phodam->registerProviderConfig($config);
    }

    /**
     * @covers ::getTypeProvider
     */
    public function testGetTypeProviderForTypeThatHasNoDefaultProvider(): void
    {
        $type = UnregisteredClassType::class;

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("Unable to find a default provider of type Phodam\Tests\Fixtures\UnregisteredClassType");

        $this->phodam->getTypeProvider($type);
    }

    /**
     * @covers ::getTypeProvider
     */
    public function testGetTypeProviderByNameForTypeThatHasNoProviders(): void
    {
        $class = UnregisteredClassType::class;
        $name = "MyName";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type Phodam\Tests\Fixtures\UnregisteredClassType with the name MyName");

        $this->phodam->getTypeProvider($class, $name);
    }

    /**
     * @covers ::getTypeProvider
     */
    public function testGetTypeProviderByNameForTypeThatHasNoNamedProviders(): void
    {
        $type = UnregisteredClassType::class;
        $name = "MyName";

        $config = (new ProviderConfig($this->provider))
            ->forType($type);
        $this->phodam->registerProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type Phodam\Tests\Fixtures\UnregisteredClassType with the name MyName");

        $this->phodam->getTypeProvider($type, $name);
    }


    /**
     * @covers ::getTypeProvider
     */
    public function testGetTypeProviderByNameForTypeWithoutRegisteredName(): void
    {
        $type = UnregisteredClassType::class;
        $name = "MyName";

        $config = (new ProviderConfig($this->provider))
            ->forType($type)
            ->withName("SomeOtherName");

        $this->phodam->registerProviderConfig($config);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to find a provider of type Phodam\Tests\Fixtures\UnregisteredClassType with the name MyName");

        $this->phodam->getTypeProvider($type, $name);
    }

    /**
     * @covers ::createArray
     */
    public function testCreateArray(): void
    {
        $name = 'MyArrayName';
        $overrides = [
            'field1' => 'my first value'
        ];
        $expectedArray = [
            'field1' => 'my first value',
            'field2' => 'second value'
        ];

        $provider = new SampleArrayProvider();
        $config = (new ProviderConfig($provider))->forArray()->withName($name);
        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->createArray($name, $overrides);
        $this->assertEquals($expectedArray, $result);
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $name = 'MyUnregisteredClassType';
        $overrides = [
            'field1' => 'my overridden value'
        ];

        $provider = new SampleProvider();
        $config = (new ProviderConfig($provider))->forType(UnregisteredClassType::class)->withName($name);
        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->create(UnregisteredClassType::class, $name, $overrides);

        $this->assertEquals('my overridden value', $result->getField1());
        $this->assertIsString($result->getField2());
        $this->assertNotEquals('second value', $result->getField2());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutTypeProviderExisting(): void
    {
        $result = $this->phodam->create(SimpleType::class);

        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertIsBool($result->isMyBool());
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutTypeProviderExistingAndWithOverrides(): void
    {
        $overrides = [
            'myFloat' => 98.1,
            'myString' => 'Cool String'
        ];

        $result = $this->phodam->create(
            SimpleType::class,
            null,
            $overrides
        );

        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertEquals($overrides['myFloat'], $result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertEquals($overrides['myString'], $result->getMyString());
        $this->assertIsBool($result->isMyBool());
    }

    public function testCreateWithBuiltinString(): void
    {
        $result = $this->phodam->create('string');
        $this->assertIsString($result, 'Expected result should be a string');
    }

    public function testCreateWithBuiltinInt(): void
    {
        $result = $this->phodam->create('int');
        $this->assertIsInt($result, 'Expected result should be an int');
    }

    public function testCreateWithBuiltinFloat(): void
    {
        $result = $this->phodam->create('float');
        $this->assertIsFloat($result, 'Expected result should be a float');
    }
}
