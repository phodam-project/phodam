<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider;

use Phodam\Analyzer\FieldDefinition;
use Phodam\PhodamInterface;
use Phodam\Provider\DefinitionBasedTypeProvider;
use Phodam\Provider\UnableToGenerateTypeException;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\SimpleTypeWithAnArray;
use PhodamTests\Fixtures\SimpleTypeWithoutTypes;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Provider\DefinitionBasedTypeProvider
 */
class DefinitionBasedTypeProviderTest extends PhodamBaseTestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
     */
    public function testCreate()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $myString = 'My String';
        $myBool = false;

        $this->phodam->expects($this->exactly(4))
            ->method('create')
            ->willReturnMap([
                [ 'int', null, [], [], $myInt ],
                [ 'float', null, [], [], $myFloat ],
                [ 'string', null, [], [], $myString ],
                [ 'bool', null, [], [], $myBool ]
            ]);

        $type = SimpleType::class;
        $definition = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];

        $provider = new DefinitionBasedTypeProvider($type, $definition);
        $provider->setPhodam($this->phodam);

        $result = $provider->create();
        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($myString, $result->getMyString());
        $this->assertEquals($myBool, $result->isMyBool());
    }


    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
     */
    public function testCreateWithNamedProvider()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $myString = 'My Named String';
        $myBool = false;

        $this->phodam->expects($this->exactly(4))
            ->method('create')
            ->willReturnMap([
                [ 'int', null, [], [], $myInt ],
                [ 'float', null, [], [], $myFloat ],
                [ 'string', 'MyNamedString', [], [], $myString ],
                [ 'bool', null, [], [], $myBool ]
            ]);

        $type = SimpleType::class;
        $definition = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setName('MyNamedString')
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];

        $provider = new DefinitionBasedTypeProvider($type, $definition);
        $provider->setPhodam($this->phodam);

        $result = $provider->create();
        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($myString, $result->getMyString());
        $this->assertEquals($myBool, $result->isMyBool());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
     */
    public function testCreateWithOverrides()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $overrides = [
            'myString' => 'Overridden String',
            'myBool' => true
        ];

        $this->phodam->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                [ 'int', null, [], [], $myInt ],
                [ 'float', null, [], [], $myFloat ]
            ]);

        $type = SimpleType::class;
        $definition = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];

        $provider = new DefinitionBasedTypeProvider($type, $definition);
        $provider->setPhodam($this->phodam);

        $result = $provider->create($overrides);
        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($overrides['myString'], $result->getMyString());
        $this->assertEquals($overrides['myBool'], $result->isMyBool());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
     */
    public function testCreateSimpleTypeWithoutTypesHasFullDefinition()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $myString = 'My String';
        $myBool = false;

        $this->phodam->expects($this->exactly(4))
            ->method('create')
            ->willReturnMap([
                [ 'int', null, [], [], $myInt ],
                [ 'float', null, [], [], $myFloat ],
                [ 'string', null, [], [], $myString ],
                [ 'bool', null, [], [], $myBool ]
            ]);

        $type = SimpleTypeWithoutTypes::class;
        $definition = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];

        $provider = new DefinitionBasedTypeProvider($type, $definition);
        $provider->setPhodam($this->phodam);

        $result = $provider->create();
        $this->assertInstanceOf($type, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($myString, $result->getMyString());
        $this->assertEquals($myBool, $result->isMyBool());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
     */
    public function testCreateSimpleTypeMissingSomeFieldsButDefined()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $myString = 'My String';
        $myBool = false;

        $this->phodam->expects($this->exactly(4))
            ->method('create')
            ->willReturnMap([
                [ 'int', null, [], [], $myInt ],
                [ 'float', null, [], [], $myFloat ],
                [ 'string', null, [], [], $myString ],
                [ 'bool', null, [], [], $myBool ]
            ]);

        $type = SimpleTypeMissingSomeFieldTypes::class;
        $definition = [
            'myInt' => new FieldDefinition('int'),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true)
        ];

        $provider = new DefinitionBasedTypeProvider($type, $definition);
        $provider->setPhodam($this->phodam);

        $result = $provider->create();
        $this->assertInstanceOf($type, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($myString, $result->getMyString());
        $this->assertEquals($myBool, $result->isMyBool());
    }

    /**
     * @covers ::__construct
     * @covers ::create
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
     */
    public function testCreateSimpleTypeMissingSomeFieldsNotAllDefined()
    {
        $this->phodam->expects($this->never())
            ->method('create');

        $type = SimpleTypeMissingSomeFieldTypes::class;
        $definition = [
            'myInt' => [
                'type' => 'int',
                'nullable' => false,
                'array' => false
            ]
        ];

        $provider = new DefinitionBasedTypeProvider($type, $definition);
        $provider->setPhodam($this->phodam);

        $this->expectException(UnableToGenerateTypeException::class);
        $this->expectExceptionMessage(
            'PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes: Unable to map fields myString'
        );

        $result = $provider->create();
    }

    /**
     * @covers ::create
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
     */
    public function testCreateSimpleTypeWithAnArray()
    {
        $type = SimpleTypeWithAnArray::class;
        $definition = [
            'myInt' => new FieldDefinition('int'),
            'myArray' => (new FieldDefinition(SimpleType::class))
                ->setArray(true)
        ];

        $this->phodam
            ->method('create')
            ->willReturnCallback(function ($arg1) {
                if ($arg1 === 'int') {
                    return 10;
                } else if ($arg1 === SimpleType::class) {
                    return (new SimpleType())
                        ->setMyBool(true)
                        ->setMyInt(rand(0, 20))
                        ->setMyString('hi there')
                        ->setMyFloat(123.45);
                }
                return null;
            });

        $provider = new DefinitionBasedTypeProvider($type, $definition);
        $provider->setPhodam($this->phodam);

        /** @var SimpleTypeWithAnArray $created */
        $created = $provider->create();
        // var_export($created);
        $this->assertInstanceOf($type, $created);
        $this->assertIsInt($created->getMyInt());
        $this->assertIsArray($created->getMyArray());
        foreach ($created->getMyArray() as $myArrayEntry) {
            $this->assertInstanceOf(SimpleType::class, $myArrayEntry);
        }
    }
}
