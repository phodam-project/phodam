<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\Advanced;

use Phodam\PhodamSchema;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(\Phodam\Phodam::class)]
#[CoversClass(\Phodam\Provider\ProviderContext::class)]
class CustomProviderIntegrationTest extends IntegrationBaseTestCase
{
    public function testCustomProviderIsUsed(): void
    {
        $schema = PhodamSchema::blank();
        $customProvider = $this->createMock(ProviderInterface::class);
        $customProvider->expects($this->once())
            ->method('create')
            ->willReturn(new UnregisteredClassType('test', 'test', 1));

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($customProvider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testCustomProviderReceivesContext(): void
    {
        $schema = PhodamSchema::blank();
        $customProvider = $this->createMock(ProviderInterface::class);
        $customProvider->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(ProviderContextInterface::class))
            ->willReturn(new UnregisteredClassType('test', 'test', 1));

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($customProvider);

        $phodam = $schema->getPhodam();
        $phodam->create(UnregisteredClassType::class);
    }

    public function testCustomProviderCanUseOverrides(): void
    {
        $schema = PhodamSchema::blank();
        $customProvider = $this->createMock(ProviderInterface::class);
        $customProvider->expects($this->once())
            ->method('create')
            ->willReturnCallback(function (ProviderContextInterface $context) {
                $overrides = $context->getOverrides();
                return new UnregisteredClassType(
                    $overrides['field1'] ?? 'default1',
                    $overrides['field2'] ?? 'default2',
                    $overrides['field3'] ?? 0
                );
            });

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($customProvider);

        $phodam = $schema->getPhodam();
        $overrides = ['field1' => 'custom value'];
        $result = $phodam->create(UnregisteredClassType::class, null, $overrides);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertEquals('custom value', $result->getField1());
    }

    public function testCustomProviderCanUseConfig(): void
    {
        $schema = PhodamSchema::blank();
        $customProvider = $this->createMock(ProviderInterface::class);
        $customProvider->expects($this->once())
            ->method('create')
            ->willReturnCallback(function (ProviderContextInterface $context) {
                $config = $context->getConfig();
                return new UnregisteredClassType(
                    'test',
                    'test',
                    $config['customValue'] ?? 0
                );
            });

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($customProvider);

        $phodam = $schema->getPhodam();
        $config = ['customValue' => 42];
        $result = $phodam->create(UnregisteredClassType::class, null, null, $config);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertEquals(42, $result->getField3());
    }

    public function testCustomProviderCanCreateNestedObjects(): void
    {
        $schema = PhodamSchema::withDefaults();
        $customProvider = new class implements ProviderInterface {
            public function create(ProviderContextInterface $context)
            {
                $phodam = $context->getPhodam();
                return new UnregisteredClassType(
                    $phodam->create('string'),
                    $phodam->create('string'),
                    $phodam->create('int')
                );
            }
        };

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($customProvider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertIsString($result->getField1());
        $this->assertIsString($result->getField2());
        $this->assertIsInt($result->getField3());
    }

    public function testCustomProviderWithNamedProvider(): void
    {
        $schema = PhodamSchema::blank();
        $customProvider = $this->createMock(ProviderInterface::class);
        $customProvider->expects($this->once())
            ->method('create')
            ->willReturn(new UnregisteredClassType('test', 'test', 1));

        $schema->forType(UnregisteredClassType::class)
            ->withName('custom')
            ->registerProvider($customProvider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'custom');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }
}

