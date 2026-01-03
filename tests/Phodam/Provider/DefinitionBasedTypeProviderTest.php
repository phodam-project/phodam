<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider;

use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use Phodam\PhodamInterface;
use Phodam\Provider\DefinitionBasedTypeProvider;
use Phodam\Provider\IncompleteDefinitionException;
use Phodam\Provider\ProviderContext;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\SimpleTypeWithAnArray;
use PhodamTests\Fixtures\SimpleTypeWithoutTypes;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(DefinitionBasedTypeProvider::class)]
#[CoversMethod(DefinitionBasedTypeProvider::class, '__construct')]
#[CoversMethod(DefinitionBasedTypeProvider::class, 'create')]
#[CoversMethod(DefinitionBasedTypeProvider::class, 'analyze')]
class DefinitionBasedTypeProviderTest extends PhodamBaseTestCase
{
    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    public function testCreate()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $myString = 'My String';
        $myBool = false;

        $this->phodam->expects($this->exactly(4))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $myInt,
                $myFloat,
                $myString,
                $myBool
            );
        // TODO: We should be checking this, but I can't get it to work right now...
//            ->willReturnMap([
//                [ 'int', $this->anything(), $this->anything(), $this->anything(), $myInt ],
//                [ 'float', $this->anything(), $this->anything(), $this->anything(), $myFloat ],
//                [ 'string', $this->anything(), $this->anything(), $this->anything(), $myString ],
//                [ 'bool', $this->anything(), $this->anything(), $this->anything(), $myBool ]
//            ]);

        $type = SimpleType::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];
        $definition = new TypeDefinition($type, null, false, $fields);

        $provider = new DefinitionBasedTypeProvider($definition);

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


    public function testCreateWithNamedProvider()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $myString = 'My Named String';
        $myBool = false;

        $this->phodam->expects($this->exactly(4))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $myInt,
                $myFloat,
                $myString,
                $myBool
            );
        // TODO: We should be checking this, but I can't get it to work right now...
//            ->willReturnMap([
//                [ 'int', $this->anything(), $this->anything(), $this->anything(), $myInt ],
//                [ 'float', $this->anything(), $this->anything(), $this->anything(), $myFloat ],
//                [ 'string', 'MyNamedString', $this->anything(), $this->anything(), $myString ],
//                [ 'bool', $this->anything(), $this->anything(), $this->anything(), $myBool ]
//            ]);

        $type = SimpleType::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setName('MyNamedString')
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];
        $definition = new TypeDefinition($type, null, false, $fields);

        $provider = new DefinitionBasedTypeProvider($definition);

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
            ->willReturnOnConsecutiveCalls(
                $myInt,
                $myFloat
            );
        // TODO: We should be checking this, but I can't get it to work right now...
//            ->willReturnMap([
//                [ 'int', $this->anything(), $this->anything(), $this->anything(), $myInt ],
//                [ 'float', $this->anything(), $this->anything(), $this->anything(), $myFloat ],
//                [ 'string', $this->anything(), $this->anything(), $this->anything(), $myString ],
//                [ 'bool', $this->anything(), $this->anything(), $this->anything(), $myBool ]
//            ]);

        $type = SimpleType::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];
        $definition = new TypeDefinition($type, null, false, $fields);

        $provider = new DefinitionBasedTypeProvider($definition);

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

    public function testCreateSimpleTypeWithoutTypesHasFullDefinition()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $myString = 'My String';
        $myBool = false;

        $this->phodam->expects($this->exactly(4))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $myInt,
                $myFloat,
                $myString,
                $myBool
            );
        // TODO: We should be checking this, but I can't get it to work right now...
//            ->willReturnMap([
//                [ 'int', $this->anything(), $this->anything(), $this->anything(), $myInt ],
//                [ 'float', $this->anything(), $this->anything(), $this->anything(), $myFloat ],
//                [ 'string', $this->anything(), $this->anything(), $this->anything(), $myString ],
//                [ 'bool', $this->anything(), $this->anything(), $this->anything(), $myBool ]
//            ]);

        $type = SimpleTypeWithoutTypes::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myFloat' => (new FieldDefinition('float'))
                ->setNullable(true),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true),
            'myBool' => new FieldDefinition('bool')
        ];
        $definition = new TypeDefinition($type, null, false, $fields);

        $provider = new DefinitionBasedTypeProvider($definition);

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

    public function testCreateSimpleTypeMissingSomeFieldsButDefined()
    {
        $myInt = 42;
        $myFloat = 98.6;
        $myString = 'My String';
        $myBool = false;

        $this->phodam->expects($this->exactly(4))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $myInt,
                $myString,
                $myFloat,
                $myBool
            );
        // TODO: We should be checking this, but I can't get it to work right now...
//            ->willReturnMap([
//                [ 'int', $this->anything(), $this->anything(), $this->anything(), $myInt ],
//                [ 'float', $this->anything(), $this->anything(), $this->anything(), $myFloat ],
//                [ 'string', $this->anything(), $this->anything(), $this->anything(), $myString ],
//                [ 'bool', $this->anything(), $this->anything(), $this->anything(), $myBool ]
//            ]);

        $type = SimpleTypeMissingSomeFieldTypes::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myString' => (new FieldDefinition('string'))
                ->setNullable(true)
        ];
        $definition = new TypeDefinition($type, null, false, $fields);

        $provider = new DefinitionBasedTypeProvider($definition);

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
        $definition = new TypeDefinition($type, null, false, $fields);

        $provider = new DefinitionBasedTypeProvider($definition);


        $context = new ProviderContext(
            $this->phodam,
            SimpleTypeMissingSomeFieldTypes::class,
            [],
            []
        );

        $this->expectException(IncompleteDefinitionException::class);
        $this->expectExceptionMessage(
            'PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes: Unable to map fields: myString'
        );

        $provider->create($context);
    }

    public function testCreateSimpleTypeWithAnArray()
    {
        $type = SimpleTypeWithAnArray::class;
        $fields = [
            'myInt' => new FieldDefinition('int'),
            'myArray' => (new FieldDefinition(SimpleType::class))
                ->setArray(true)
        ];
        $definition = new TypeDefinition($type, null, false, $fields);

        $this->phodam
            ->method('create')
            ->willReturnCallback(function ($arg1) {
                if ($arg1 === 'int') {
                    return 10;
                } elseif ($arg1 === SimpleType::class) {
                    return (new SimpleType())
                        ->setMyBool(true)
                        ->setMyInt(rand(0, 20))
                        ->setMyString('hi there')
                        ->setMyFloat(123.45);
                }
                return null;
            });

        $provider = new DefinitionBasedTypeProvider($definition);

        $context = new ProviderContext(
            $this->phodam,
            SimpleType::class,
            [],
            []
        );

        /** @var SimpleTypeWithAnArray $created */
        $created = $provider->create($context);
        $this->assertInstanceOf($type, $created);
        $this->assertIsInt($created->getMyInt());
        $this->assertIsArray($created->getMyArray());
        foreach ($created->getMyArray() as $myArrayEntry) {
            $this->assertInstanceOf(SimpleType::class, $myArrayEntry);
        }
    }
}
