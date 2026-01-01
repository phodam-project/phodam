<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ProviderManagement;

use Phodam\PhodamSchema;
use PhodamTests\Fixtures\SampleProvider;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Store\Registrar::class)]
class ProviderOverrideTest extends IntegrationBaseTestCase
{
    public function testOverrideDefaultProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider1 = new SampleProvider();
        $provider2 = new SampleProvider();

        // Register first provider
        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($provider1);

        // Override with second provider
        $schema->forType(UnregisteredClassType::class)
            ->overriding()
            ->registerProvider($provider2);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testOverrideNamedProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider1 = new SampleProvider();
        $provider2 = new SampleProvider();

        // Register first named provider
        $schema->forType(UnregisteredClassType::class)
            ->withName('myProvider')
            ->registerProvider($provider1);

        // Override with second provider
        $schema->forType(UnregisteredClassType::class)
            ->withName('myProvider')
            ->overriding()
            ->registerProvider($provider2);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'myProvider');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testOverrideReplacesExistingProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider1 = new SampleProvider();
        $provider2 = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($provider1);

        $schema->forType(UnregisteredClassType::class)
            ->overriding()
            ->registerProvider($provider2);

        $phodam = $schema->getPhodam();

        // Should use provider2, not provider1
        $result = $phodam->create(UnregisteredClassType::class);
        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testOverrideWithDifferentBehavior(): void
    {
        $schema = PhodamSchema::blank();

        // Register default string provider
        $schema->forType('string')
            ->registerProvider(\Phodam\Provider\Primitive\DefaultStringTypeProvider::class);

        // Override with a custom provider that always returns the same value
        $customProvider = $this->createMock(\Phodam\Provider\ProviderInterface::class);
        $customProvider->method('create')
            ->willReturn('custom value');

        $schema->forType('string')
            ->overriding()
            ->registerProvider($customProvider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertEquals('custom value', $result);
    }

    public function testOverridePreservesType(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($provider);

        $schema->forType(UnregisteredClassType::class)
            ->overriding()
            ->registerProvider(new SampleProvider());

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }
}

