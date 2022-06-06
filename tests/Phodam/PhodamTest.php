<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam;

use InvalidArgumentException;
use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Phodam;
use Phodam\Provider\ProviderConfig;
use Phodam\Provider\ProviderNotFoundException;
use PhodamTests\Fixtures\SampleArrayProvider;
use PhodamTests\Fixtures\SampleProvider;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\UnregisteredClassType;

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
            "A type provider of type PhodamTests\Fixtures\UnregisteredClassType with the name MyCoolClassProvider already exists"
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
        $this->expectExceptionMessage("Unable to find a default provider of type PhodamTests\Fixtures\UnregisteredClassType");

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
        $this->expectExceptionMessage("Unable to find a provider of type PhodamTests\Fixtures\UnregisteredClassType with the name MyName");

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
        $this->expectExceptionMessage("Unable to find a provider of type PhodamTests\Fixtures\UnregisteredClassType with the name MyName");

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
        $this->expectExceptionMessage("Unable to find a provider of type PhodamTests\Fixtures\UnregisteredClassType with the name MyName");

        $this->phodam->getTypeProvider($type, $name);
    }

    /**
     * Tests that a TypeProvider doesn't exist for a type, then after registering a definition,
     * it can create a value of that type
     *
     * @covers ::registerTypeDefinition
     */
    public function testRegisterTypeDefinition(): void
    {
        $type = SimpleTypeMissingSomeFieldTypes::class;
        $definition = [
            'myInt' => [
                'type' => 'int'
            ],
            'myFloat' => [
                'type' => 'float',
                'nullable' => true
            ],
            'myString' => [
                'type' => 'string'
            ],
            'myBool' => [
                'type' => 'bool'
            ]
        ];

        // try getting a type provider that exists already, it shouldn't, so an exception should be thrown
        try {
            $this->phodam->getTypeProvider($type);
        } catch (ProviderNotFoundException $ex) {
            $this->assertTrue(true,  "Provider was found, it shouldn't have been");
        }

        // try creating an object of the type, it shouldn't find a type provider
        // so when it doesn't find one, it will try to analyze the type to create a definition
        // it shouldn't be able to since it's not a well-defined type
        try {
            $this->phodam->create($type);
        } catch (TypeAnalysisException $ex) {
            $this->assertInstanceOf(TypeAnalysisException::class, $ex);
            $this->assertEquals(
                'Phodam\\Tests\\Fixtures\\SimpleTypeMissingSomeFieldTypes: Unable to map fields: myInt, myString',
                $ex->getMessage()
            );
        }

        $this->phodam->registerTypeDefinition($type, $definition);

        $result = $this->phodam->create($type);
        $this->assertInstanceOf(SimpleTypeMissingSomeFieldTypes::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertIsBool($result->isMyBool());
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

        $provider = new SampleArrayProvider();
        $config = (new ProviderConfig($provider))->forArray()->withName($name);
        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->createArray($name, $overrides);
        $this->assertIsArray($result);
        $this->assertEquals('my first value', $result['field1']);
        $this->assertEquals('second value', $result['field2']);
        $this->assertIsInt($result['field3']);
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

        $customConfig = [
            'minYear' => 1990,
            'maxYear' => 2000
        ];
        $result = $this->phodam->create(UnregisteredClassType::class, $name, $overrides, $customConfig);
        $this->assertEquals('my overridden value', $result->getField1());
        $this->assertIsString($result->getField2());
        $this->assertNotEquals('second value', $result->getField2());
        $this->assertIsInt($result->getField3());
        $this->assertGreaterThanOrEqual(1990, $result->getField3());
        $this->assertLessThanOrEqual(2000, $result->getField3());
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
