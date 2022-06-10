<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider;

use Phodam\PhodamInterface;
use Phodam\Provider\DefinitionBasedTypeProvider;
use Phodam\Provider\ProviderContext;
use Phodam\Provider\UnableToGenerateTypeException;
use Phodam\Tests\Fixtures\SimpleType;
use Phodam\Tests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use Phodam\Tests\Fixtures\SimpleTypeWithoutTypes;
use Phodam\Tests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

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
     * @uses \Phodam\Provider\ProviderContext
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
        $definition = [
            'myInt' => [
                'type' => 'int',
                'nullable' => false,
                'array' => false
            ],
            'myFloat' => [
                'type' => 'float',
                'nullable' => true,
                'array' => false
            ],
            'myString' => [
                'type' => 'string',
                'nullable' => true,
                'array' => false
            ],
            'myBool' => [
                'type' => 'bool',
                'nullable' => false,
                'array' => false
            ]
        ];

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
     * @uses \Phodam\Provider\ProviderContext
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
        ];

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
     * @uses \Phodam\Provider\ProviderContext
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
        $definition = [
            'myInt' => [
                'type' => 'int',
                'nullable' => false,
                'array' => false
            ],
            'myFloat' => [
                'type' => 'float',
                'nullable' => true,
                'array' => false
            ],
            'myString' => [
                'type' => 'string',
                'nullable' => true,
                'array' => false
            ],
            'myBool' => [
                'type' => 'bool',
                'nullable' => false,
                'array' => false
            ]
        ];

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
     * @uses \Phodam\Provider\ProviderContext
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
        $definition = [
            'myInt' => [
                'type' => 'int',
                'nullable' => false,
                'array' => false
            ],
            'myFloat' => [
                'type' => 'float',
                'nullable' => true,
                'array' => false
            ],
            'myString' => [
                'type' => 'string',
                'nullable' => true,
                'array' => false
            ],
            'myBool' => [
                'type' => 'bool',
                'nullable' => false,
                'array' => false
            ]
        ];

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
     * @uses \Phodam\Provider\ProviderContext
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
        $definition = [
            'myInt' => [
                'type' => 'int',
                'nullable' => false,
                'array' => false
            ],
            'myString' => [
                'type' => 'string',
                'nullable' => true,
                'array' => false
            ]
        ];

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
     * @uses \Phodam\Provider\ProviderContext
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

        $context = new ProviderContext(
            $this->phodam,
            SimpleTypeMissingSomeFieldTypes::class,
            [],
            []
        );

        $this->expectException(UnableToGenerateTypeException::class);
        $this->expectExceptionMessage(
            'Phodam\Tests\Fixtures\SimpleTypeMissingSomeFieldTypes: Unable to map fields myString'
        );

        $provider->create($context);
    }
}
