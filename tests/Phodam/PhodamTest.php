<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam;

use InvalidArgumentException;
use Phodam\Types\FieldDefinition;
use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Types\TypeDefinition;
use Phodam\Phodam;
use Phodam\PhodamSchema;
use Phodam\Provider\ProviderConfig;
use Phodam\Store\ProviderConflictException;
use Phodam\Store\ProviderNotFoundException;
use PhodamTests\Fixtures\SampleArrayProvider;
use PhodamTests\Fixtures\SampleProvider;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\UnregisteredClassType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(Phodam::class)]
#[CoversMethod(Phodam::class, '__construct')]
#[CoversMethod(Phodam::class, 'registerPrimitiveTypeProviders')]
#[CoversMethod(Phodam::class, 'registerProviderConfig')]
#[CoversMethod(Phodam::class, 'getArrayProvider')]
#[CoversMethod(Phodam::class, 'registerArrayProviderConfig')]
#[CoversMethod(Phodam::class, 'registerTypeProviderConfig')]
#[CoversMethod(Phodam::class, 'getTypeProvider')]
#[CoversMethod(Phodam::class, 'registerTypeDefinition')]
#[CoversMethod(Phodam::class, 'createArray')]
#[CoversMethod(Phodam::class, 'create')]
#[CoversMethod(Phodam::class, 'isEnum')]
#[CoversMethod(Phodam::class, 'getOrRegisterEnumProvider')]
class PhodamTest extends PhodamBaseTestCase
{
    private Phodam $phodam;
    private SampleProvider $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->phodam = PhodamSchema::withDefaults()->getPhodam();
        $this->provider = new SampleProvider();
    }

    public function testRegisterProviderConfigWithoutValidConfig(): void
    {
        // without any configuring, we don't know if it's
        // an array or a type
        $config = new ProviderConfig($this->provider);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("A provider config must be declared for an array or a type");

        $this->phodam->registerProviderConfig($config);
    }

    public function testRegisterProviderConfigForArray(): void
    {
        $name = "MyCoolArray";

        $config = (new ProviderConfig($this->provider))
            ->forType('array')
            ->withName($name);

        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->getArrayProvider($name);

        $this->assertSame($this->provider, $result);
    }

    public function testRegisterProviderConfigForArrayWithNameThatAlreadyExists(): void
    {
        $name = "MyCoolArray";
        $config = (new ProviderConfig($this->provider))
            ->forType('array')
            ->withName($name);

        $this->phodam->registerProviderConfig($config);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage(
            "Provider for type array with name MyCoolArray already registered"
        );

        $this->phodam->registerProviderConfig($config);
    }

    public function testGetArrayProviderWithoutRegisteredName(): void
    {
        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No provider found for type array with name MyUnregisteredName");

        $this->phodam->getArrayProvider("MyUnregisteredName");
    }

    public function testRegisterProviderConfigForClassWithoutName(): void
    {
        $type = UnregisteredClassType::class;
        $config = (new ProviderConfig($this->provider))
            ->forType($type);

        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->getTypeProvider($type);

        $this->assertSame($this->provider, $result);
    }

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

    public function testRegisterProviderConfigForTypeWithNameThatAlreadyExists(): void
    {
        $type = UnregisteredClassType::class;
        $name = "MyCoolClassProvider";
        $config = (new ProviderConfig($this->provider))
            ->forType($type)
            ->withName($name);

        $this->phodam->registerProviderConfig($config);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage(
            "Provider for type PhodamTests\Fixtures\UnregisteredClassType with name MyCoolClassProvider already registered"
        );

        $this->phodam->registerProviderConfig($config);
    }

    public function testGetTypeProviderForTypeThatHasNoDefaultProvider(): void
    {
        $type = UnregisteredClassType::class;

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No default provider found for type PhodamTests\Fixtures\UnregisteredClassType");

        $this->phodam->getTypeProvider($type);
    }

    public function testGetTypeProviderByNameForTypeThatHasNoProviders(): void
    {
        $class = UnregisteredClassType::class;
        $name = "MyName";

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No provider found for type PhodamTests\Fixtures\UnregisteredClassType with name MyName");

        $this->phodam->getTypeProvider($class, $name);
    }

    public function testGetTypeProviderByNameForTypeThatHasNoNamedProviders(): void
    {
        $type = UnregisteredClassType::class;
        $name = "MyName";

        $config = (new ProviderConfig($this->provider))
            ->forType($type);
        $this->phodam->registerProviderConfig($config);

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No provider found for type PhodamTests\Fixtures\UnregisteredClassType with name MyName");

        $this->phodam->getTypeProvider($type, $name);
    }


    public function testGetTypeProviderByNameForTypeWithoutRegisteredName(): void
    {
        $type = UnregisteredClassType::class;
        $name = "MyName";

        $config = (new ProviderConfig($this->provider))
            ->forType($type)
            ->withName("SomeOtherName");

        $this->phodam->registerProviderConfig($config);

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No provider found for type PhodamTests\Fixtures\UnregisteredClassType with name MyName");

        $this->phodam->getTypeProvider($type, $name);
    }

    /**
     * Tests that a TypeProvider doesn't exist for a type, then after registering a definition,
     * it can create a value of that type
     */
    public function testRegisterTypeDefinition(): void
    {
        $type = SimpleTypeMissingSomeFieldTypes::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => new FieldDefinition('float', nullable: true),
            'myString' => new FieldDefinition('string'),
            'myBool' => new FieldDefinition('bool')
        ];
        $definition = new TypeDefinition($type, null, false, $fields);

        // try getting a type provider that exists already, it shouldn't, so an exception should be thrown
        try {
            $this->phodam->getTypeProvider($type);
        } catch (ProviderNotFoundException $ex) {
            $this->assertTrue(true, "Provider was found, it shouldn't have been");
        }

        // try creating an object of the type, it shouldn't find a type provider
        // so when it doesn't find one, it will try to analyze the type to create a definition
        // it shouldn't be able to since it's not a well-defined type
        try {
            $this->phodam->create($type);
        } catch (TypeAnalysisException $ex) {
            $this->assertInstanceOf(TypeAnalysisException::class, $ex);
            $this->assertEquals(
                'PhodamTests\\Fixtures\\SimpleTypeMissingSomeFieldTypes: Unable to map fields: myInt, myString',
                $ex->getMessage()
            );
        }

        $this->phodam->registerTypeDefinition($definition);

        $result = $this->phodam->create($type);
        $this->assertInstanceOf(SimpleTypeMissingSomeFieldTypes::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertIsBool($result->isMyBool());
    }

    public function testCreateArray(): void
    {
        $name = 'MyArrayName';
        $overrides = [
            'field1' => 'my first value'
        ];

        $provider = new SampleArrayProvider();
        $config = (new ProviderConfig($provider))->forType('array')->withName($name);
        $this->phodam->registerProviderConfig($config);

        $result = $this->phodam->createArray($name, $overrides);
        $this->assertIsArray($result);
        $this->assertEquals('my first value', $result['field1']);
        $this->assertEquals('second value', $result['field2']);
        $this->assertIsInt($result['field3']);
    }

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

    public function testCreateWithoutTypeProviderExisting(): void
    {
        $result = $this->phodam->create(SimpleType::class);

        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertIsBool($result->isMyBool());
    }

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

    public function testCreateWithPureEnum(): void
    {
        $result = $this->phodam->create(TestPureEnum::class);
        $this->assertInstanceOf(TestPureEnum::class, $result);
        $this->assertContains($result, TestPureEnum::cases());
    }

    public function testCreateWithStringBackedEnum(): void
    {
        $result = $this->phodam->create(TestStringBackedEnum::class);
        $this->assertInstanceOf(TestStringBackedEnum::class, $result);
        $this->assertContains($result, TestStringBackedEnum::cases());
        $this->assertIsString($result->value);
    }

    public function testCreateWithIntBackedEnum(): void
    {
        $result = $this->phodam->create(TestIntBackedEnum::class);
        $this->assertInstanceOf(TestIntBackedEnum::class, $result);
        $this->assertContains($result, TestIntBackedEnum::cases());
        $this->assertIsInt($result->value);
    }

    public function testCreateWithEnumReturnsRandomCase(): void
    {
        // Create multiple instances to verify randomness
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $results[] = $this->phodam->create(TestPureEnum::class);
        }

        // Verify all results are valid enum cases
        foreach ($results as $result) {
            $this->assertInstanceOf(TestPureEnum::class, $result);
            $this->assertContains($result, TestPureEnum::cases());
        }
    }

    public function testCreateWithEnumRegistersProvider(): void
    {
        // First call should register the provider
        $result1 = $this->phodam->create(TestPureEnum::class);
        $this->assertInstanceOf(TestPureEnum::class, $result1);

        // Second call should use the registered provider
        $result2 = $this->phodam->create(TestPureEnum::class);
        $this->assertInstanceOf(TestPureEnum::class, $result2);

        // Provider should now be registered
        $provider = $this->phodam->getTypeProvider(TestPureEnum::class);
        $this->assertNotNull($provider);
    }

    public function testCreateWithEnumAndOverrides(): void
    {
        // Overrides shouldn't affect enum creation, but should not cause errors
        $result = $this->phodam->create(
            TestPureEnum::class,
            null,
            ['someOverride' => 'value']
        );

        $this->assertInstanceOf(TestPureEnum::class, $result);
    }

    public function testCreateWithEnumAndConfig(): void
    {
        // Config shouldn't affect enum creation, but should not cause errors
        $result = $this->phodam->create(
            TestPureEnum::class,
            null,
            null,
            ['someConfig' => 'value']
        );

        $this->assertInstanceOf(TestPureEnum::class, $result);
    }
}

/**
 * Test enum for pure enums (UnitEnum)
 */
enum TestPureEnum
{
    case CASE_ONE;
    case CASE_TWO;
    case CASE_THREE;
}

/**
 * Test enum for string-backed enums (BackedEnum)
 */
enum TestStringBackedEnum: string
{
    case RED = 'red';
    case GREEN = 'green';
    case BLUE = 'blue';
}

/**
 * Test enum for int-backed enums (BackedEnum)
 */
enum TestIntBackedEnum: int
{
    case ZERO = 0;
    case ONE = 1;
    case TWO = 2;
}
