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

#[CoversClass(\Phodam\Phodam::class)]
#[CoversClass(\Phodam\Store\Registrar::class)]
class NamedProviderTest extends IntegrationBaseTestCase
{
    public function testRegisterNamedProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->withName('customProvider')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'customProvider');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testCreateWithNamedProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->withName('myProvider')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'myProvider');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testMultipleNamedProvidersForSameType(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider1 = new SampleProvider();
        $provider2 = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->withName('provider1')
            ->registerProvider($provider1);

        $schema->forType(UnregisteredClassType::class)
            ->withName('provider2')
            ->registerProvider($provider2);

        $phodam = $schema->getPhodam();

        $result1 = $phodam->create(UnregisteredClassType::class, 'provider1');
        $result2 = $phodam->create(UnregisteredClassType::class, 'provider2');

        $this->assertInstanceOf(UnregisteredClassType::class, $result1);
        $this->assertInstanceOf(UnregisteredClassType::class, $result2);
    }

    public function testNamedProviderDoesNotAffectDefault(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $namedProvider = new SampleProvider();
        $defaultProvider = new SampleProvider();

        // Register default provider
        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($defaultProvider);

        // Register named provider
        $schema->forType(UnregisteredClassType::class)
            ->withName('named')
            ->registerProvider($namedProvider);

        $phodam = $schema->getPhodam();

        // Default should still work
        $defaultResult = $phodam->create(UnregisteredClassType::class);
        $this->assertInstanceOf(UnregisteredClassType::class, $defaultResult);

        // Named should also work
        $namedResult = $phodam->create(UnregisteredClassType::class, 'named');
        $this->assertInstanceOf(UnregisteredClassType::class, $namedResult);
    }

    public function testDefaultProviderStillWorksWithNamedProviders(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $defaultProvider = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($defaultProvider);

        $schema->forType(UnregisteredClassType::class)
            ->withName('named1')
            ->registerProvider(new SampleProvider());

        $schema->forType(UnregisteredClassType::class)
            ->withName('named2')
            ->registerProvider(new SampleProvider());

        $phodam = $schema->getPhodam();

        // Default should still work
        $result = $phodam->create(UnregisteredClassType::class);
        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testNamedProviderOverrides(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->withName('myProvider')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();

        $overrides = ['field1' => 'custom value'];
        $result = $phodam->create(UnregisteredClassType::class, 'myProvider', $overrides);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertEquals('custom value', $result->getField1());
    }
}

