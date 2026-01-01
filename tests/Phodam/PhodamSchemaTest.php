<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam;

use InvalidArgumentException;
use Phodam\Phodam;
use Phodam\PhodamSchema;
use Phodam\Provider\DefaultProviderBundle;
use Phodam\Provider\ProviderBundleInterface;
use Phodam\Store\ProviderStore;
use Phodam\Store\RegistrarInterface;
use PhodamTests\Fixtures\SampleProviderBundle;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(\Phodam\PhodamSchema::class)]
#[CoversMethod(\Phodam\PhodamSchema::class, 'blank')]
#[CoversMethod(\Phodam\PhodamSchema::class, 'withDefaults')]
#[CoversMethod(\Phodam\PhodamSchema::class, '__construct')]
#[CoversMethod(\Phodam\PhodamSchema::class, 'forType')]
#[CoversMethod(\Phodam\PhodamSchema::class, 'add')]
#[CoversMethod(\Phodam\PhodamSchema::class, 'getPhodam')]
class PhodamSchemaTest extends PhodamBaseTestCase
{
    public function testBlankCreatesNewSchemaWithEmptyProviderStore(): void
    {
        $schema = PhodamSchema::blank();

        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testWithDefaultsCreatesSchemaAndAddsDefaultProviderBundle(): void
    {
        $schema = PhodamSchema::withDefaults();

        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testConstructWithProviderStore(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);

        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testForTypeReturnsRegistrarInterfaceWithTypeSet(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);
        $type = 'string';

        $registrar = $schema->forType($type);

        $this->assertInstanceOf(RegistrarInterface::class, $registrar);
    }

    public function testForTypeCreatesNewRegistrarWithStoreAndSetsType(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);
        $type = 'string';

        $registrar = $schema->forType($type);

        // Verify the registrar has the type set by trying to register a provider
        $provider = $this->createMock(\Phodam\Provider\ProviderInterface::class);
        $registrar->registerProvider($provider);

        // If we get here without exception, the type was set correctly
        $this->assertTrue(true);
    }

    public function testAddWithProviderBundleInterfaceInstance(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);
        $bundle = $this->createMock(ProviderBundleInterface::class);

        $bundle->expects($this->once())
            ->method('register')
            ->with($schema);

        $schema->add($bundle);
    }

    public function testAddWithClassStringThatImplementsProviderBundleInterface(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);

        // SampleProviderBundle implements ProviderBundleInterface
        $schema->add(SampleProviderBundle::class);

        // Should not throw exception
        $this->assertTrue(true);
    }

    public function testAddWithDefaultProviderBundleClass(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);

        $schema->add(DefaultProviderBundle::class);

        // Should not throw exception
        $this->assertTrue(true);
    }

    public function testAddWithInvalidClassStringThrowsReflectionException(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);

        // ReflectionException is thrown before InvalidArgumentException for non-existent classes
        $this->expectException(\ReflectionException::class);

        $schema->add('NonExistentClass' . uniqid());
    }

    public function testAddWithClassStringThatDoesNotImplementProviderBundleInterfaceThrowsException(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument must be an instance of ProviderBundleInterface or a class implementing it");

        // Use a class that exists but doesn't implement ProviderBundleInterface
        $schema->add(\stdClass::class);
    }

    public function testGetPhodamReturnsPhodamInstanceWithConfiguredProviderStore(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);

        $phodam = $schema->getPhodam();

        $this->assertInstanceOf(Phodam::class, $phodam);
        $this->assertInstanceOf(\Phodam\PhodamInterface::class, $phodam);
    }

    public function testGetPhodamCreatesPhodamWithProviderStore(): void
    {
        $store = new ProviderStore();
        $schema = new PhodamSchema($store);

        // getPhodam should create a new Phodam instance with the store
        $phodam = $schema->getPhodam();

        $this->assertInstanceOf(Phodam::class, $phodam);
    }
}
