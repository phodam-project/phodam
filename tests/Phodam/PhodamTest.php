<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam;

use Phodam\Phodam;
use Phodam\PhodamTypes;
use Phodam\Provider\TypeProviderConfig;
use Phodam\Provider\TypeProviderFactory;
use Phodam\Provider\TypeProviderInterface;
use Phodam\Tests\Fixtures\SampleTypeProvider;
use Phodam\Tests\Fixtures\UnregisteredClassType;

/**
 * @coversDefaultClass \Phodam\Phodam
 * @covers ::__construct
 */
class PhodamTest extends PhodamTestCase
{
    private Phodam $phodam;
    private TypeProviderFactory $typeProviderFactoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->typeProviderFactoryMock =
            $this->createMock(TypeProviderFactory::class);

        $this->phodam = new Phodam($this->typeProviderFactoryMock);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorWithoutFactory(): void
    {
        // covers the case where there is no passed in Factory
        $phodam = new Phodam();
        $this->assertInstanceOf(Phodam::class, $phodam);;
    }


    /**
     * @covers ::registerTypeProviderConfig
     */
    public function testRegisterTypeProviderConfig(): void
    {
        $typeProvider = new SampleTypeProvider();
        $config = new TypeProviderConfig($typeProvider);

        $this->typeProviderFactoryMock->expects($this->once())
            ->method('registerTypeProviderConfig')
            ->with($config);

        $result = $this->phodam->registerTypeProviderConfig($config);
        $this->assertEquals($result, $this->phodam);
    }

    /**
     * @covers ::createArray
     */
    public function testCreateArray(): void
    {
        $name = 'MyArrayName';
        $overrides = [
            'field1' => 'value'
        ];
        $createdArray = [
            'field1' => 'value',
            'field2' => 'second value'
        ];

        $provider = $this->createMock(TypeProviderInterface::class);

        $this->typeProviderFactoryMock->expects($this->once())
            ->method('getArrayProvider')
            ->with($name)
            ->willReturn($provider);

        $provider->expects($this->once())
            ->method('create')
            ->with($overrides)
            ->willReturn($createdArray);

        $result = $this->phodam->createArray($name, $overrides);
        $this->assertEquals($result, $createdArray);
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $name = 'MyUnregisteredClassType';
        $overrides = [
            'override1' => 'overrideValue1'
        ];
        $createdClass = new UnregisteredClassType();

        $provider = $this->createMock(TypeProviderInterface::class);

        $this->typeProviderFactoryMock->expects($this->once())
            ->method('getClassProvider')
            ->with(UnregisteredClassType::class, $name)
            ->willReturn($provider);

        $provider->expects($this->once())
            ->method('create')
            ->with($overrides)
            ->willReturn($createdClass);

        $result = $this->phodam->create(UnregisteredClassType::class, $name, $overrides);
        $this->assertEquals($result, $createdClass);;
    }

    /**
     * @covers ::createFloat
     */
    public function testCreateFloat(): void
    {
        $name = 'MyFloatName';
        $createdFloat = 42.0;

        $provider = $this->createMock(TypeProviderInterface::class);

        $this->typeProviderFactoryMock->expects($this->once())
            ->method('getPrimitiveProvider')
            ->with(PhodamTypes::PRIMITIVE_FLOAT, $name)
            ->willReturn($provider);

        $provider->expects($this->once())
            ->method('create')
            ->willReturn($createdFloat);

        $result = $this->phodam->createFloat($name);
        $this->assertEquals($result, $createdFloat);
    }

    /**
     * @covers ::createInt
     */
    public function testCreateInt(): void
    {
        $name = 'MyIntName';
        $createdInt = 123;

        $provider = $this->createMock(TypeProviderInterface::class);

        $this->typeProviderFactoryMock->expects($this->once())
            ->method('getPrimitiveProvider')
            ->with(PhodamTypes::PRIMITIVE_INT, $name)
            ->willReturn($provider);

        $provider->expects($this->once())
            ->method('create')
            ->willReturn($createdInt);

        $result = $this->phodam->createInt($name);
        $this->assertEquals($result, $createdInt);
    }

    /**
     * @covers ::createString
     */
    public function testCreateString(): void
    {
        $name = 'MyStringName';
        $createdString = 'abcdef01-1234-5678-1234-abcdefabcdef';

        $provider = $this->createMock(TypeProviderInterface::class);

        $this->typeProviderFactoryMock->expects($this->once())
            ->method('getPrimitiveProvider')
            ->with(PhodamTypes::PRIMITIVE_STRING, $name)
            ->willReturn($provider);

        $provider->expects($this->once())
            ->method('create')
            ->willReturn($createdString);

        $result = $this->phodam->createString($name);
        $this->assertEquals($result, $createdString);
    }
}
