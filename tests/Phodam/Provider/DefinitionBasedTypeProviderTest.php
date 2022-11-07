<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

<<<<<<< HEAD
namespace PhodamTests\Phodam\Provider;
=======
namespace Phodam\Tests\Phodam\Provider;
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13

use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeDefinition;
use Phodam\PhodamInterface;
use Phodam\Provider\DefinitionBasedTypeProvider;
<<<<<<< HEAD
use Phodam\Provider\UnableToGenerateTypeException;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\SimpleTypeWithAnArray;
use PhodamTests\Fixtures\SimpleTypeWithoutTypes;
use PhodamTests\Phodam\PhodamBaseTestCase;
=======
use Phodam\Provider\ProviderContext;
use Phodam\Provider\IncompleteDefinitionException;
use Phodam\Tests\Fixtures\SimpleType;
use Phodam\Tests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use Phodam\Tests\Fixtures\SimpleTypeWithoutTypes;
use Phodam\Tests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13

/**
 * @coversDefaultClass \Phodam\Provider\DefinitionBasedTypeProvider
 */
class DefinitionBasedTypeProviderTest extends PhodamBaseTestCase
{
    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    /**
     * @covers ::__construct
     * @covers ::create
<<<<<<< HEAD
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
=======
     * @uses \Phodam\Provider\ProviderContext
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
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
                [ 'int', null, null, null, $myInt ],
                [ 'float', null, null, null, $myFloat ],
                [ 'string', null, null, null, $myString ],
                [ 'bool', null, null, null, $myBool ]
            ]);

        $type = SimpleType::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];
        $definition = new TypeDefinition($fields);

        $provider = new DefinitionBasedTypeProvider($type, $definition);

        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            [],
            []
        );

        $result = $provider->create($context);
        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($myString, $result->getMyString());
        $this->assertEquals($myBool, $result->isMyBool());
    }


    /**
     * @covers ::__construct
     * @covers ::create
<<<<<<< HEAD
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
=======
     * @uses \Phodam\Provider\ProviderContext
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
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
                [ 'int', null, null, null, $myInt ],
                [ 'float', null, null, null, $myFloat ],
                [ 'string', 'MyNamedString', null, null, $myString ],
                [ 'bool', null, null, null, $myBool ]
            ]);

        $type = SimpleType::class;
<<<<<<< HEAD
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setName('MyNamedString')
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
=======
        $definition = [
            'myInt' => [
                'type' => 'int',
                'name' => null,
                'nullable' => false,
                'array' => false
            ],
            'myFloat' => [
                'type' => 'float',
                'name' => null,
                'nullable' => true,
                'array' => false
            ],
            'myString' => [
                'type' => 'string',
                'name' => 'MyNamedString',
                'nullable' => true,
                'array' => false
            ],
            'myBool' => [
                'type' => 'bool',
                'name' => null,
                'nullable' => false,
                'array' => false
            ]
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
        ];
        $definition = new TypeDefinition($fields);

        $provider = new DefinitionBasedTypeProvider($type, $definition);

        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            [],
            []
        );

        $result = $provider->create($context);
        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($myString, $result->getMyString());
        $this->assertEquals($myBool, $result->isMyBool());
    }

    /**
     * @covers ::__construct
     * @covers ::create
<<<<<<< HEAD
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
=======
     * @uses \Phodam\Provider\ProviderContext
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
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
                [ 'int', null, null, null, $myInt ],
                [ 'float', null, null, null, $myFloat ]
            ]);

        $type = SimpleType::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];
        $definition = new TypeDefinition($fields);

        $provider = new DefinitionBasedTypeProvider($type, $definition);

        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            $overrides,
            []
        );

        $result = $provider->create($context);
        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($overrides['myString'], $result->getMyString());
        $this->assertEquals($overrides['myBool'], $result->isMyBool());
    }

    /**
     * @covers ::__construct
     * @covers ::create
<<<<<<< HEAD
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
=======
     * @uses \Phodam\Provider\ProviderContext
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
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
                [ 'int', null, null, null, $myInt ],
                [ 'float', null, null, null, $myFloat ],
                [ 'string', null, null, null, $myString ],
                [ 'bool', null, null, null, $myBool ]
            ]);

        $type = SimpleTypeWithoutTypes::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];
        $definition = new TypeDefinition($fields);

        $provider = new DefinitionBasedTypeProvider($type, $definition);

        $context = new ProviderContext(
            $this->phodam,
            SimpleTypeWithoutTypes::class,
            [],
            []
        );

        $result = $provider->create($context);
        $this->assertInstanceOf($type, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($myString, $result->getMyString());
        $this->assertEquals($myBool, $result->isMyBool());
    }

    /**
     * @covers ::__construct
     * @covers ::create
<<<<<<< HEAD
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
=======
     * @uses \Phodam\Provider\ProviderContext
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
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
                [ 'int', null, null, null, $myInt ],
                [ 'float', null, null, null, $myFloat ],
                [ 'string', null, null, null, $myString ],
                [ 'bool', null, null, null, $myBool ]
            ]);

        $type = SimpleTypeMissingSomeFieldTypes::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true)
        ];
        $definition = new TypeDefinition($fields);

        $provider = new DefinitionBasedTypeProvider($type, $definition);

        $context = new ProviderContext(
            $this->phodam,
            SimpleTypeMissingSomeFieldTypes::class,
            [],
            []
        );

        $result = $provider->create($context);
        $this->assertInstanceOf($type, $result);
        $this->assertEquals($myInt, $result->getMyInt());
        $this->assertEquals($myFloat, $result->getMyFloat());
        $this->assertEquals($myString, $result->getMyString());
        $this->assertEquals($myBool, $result->isMyBool());
    }

    /**
     * @covers ::__construct
     * @covers ::create
<<<<<<< HEAD
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
=======
     * @uses \Phodam\Provider\ProviderContext
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
     */
    public function testCreateSimpleTypeMissingSomeFieldsNotAllDefined()
    {
        $this->phodam->expects($this->never())
            ->method('create');

        $type = SimpleTypeMissingSomeFieldTypes::class;
        $fields = [
            'myInt' => [
                'type' => 'int',
                'nullable' => false,
                'array' => false
            ]
        ];
        $definition = new TypeDefinition($fields);

        $provider = new DefinitionBasedTypeProvider($type, $definition);

<<<<<<< HEAD
        $this->expectException(UnableToGenerateTypeException::class);
        $this->expectExceptionMessage(
            'PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes: Unable to map fields myString'
=======
        $context = new ProviderContext(
            $this->phodam,
            SimpleTypeMissingSomeFieldTypes::class,
            [],
            []
>>>>>>> ffd88404ae6d0d060da7f6d70dec810feecd1a13
        );

        $this->expectException(IncompleteDefinitionException::class);
        $this->expectExceptionMessage(
            'Phodam\Tests\Fixtures\SimpleTypeMissingSomeFieldTypes: Unable to map fields: myString'
        );

        $provider->create($context);
    }

    /**
     * @covers ::create
     * @covers ::generateValueFromFieldDefinition
     * @covers ::generateSingleValueFromFieldDefinition
     */
    public function testCreateSimpleTypeWithAnArray()
    {
        $type = SimpleTypeWithAnArray::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myArray' => (new FieldDefinition(SimpleType::class))
                ->setArray(true)
        ];
        $definition = new TypeDefinition($fields);

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
