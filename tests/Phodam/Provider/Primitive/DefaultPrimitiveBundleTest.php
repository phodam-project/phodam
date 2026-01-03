<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Primitive;

use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use Phodam\Provider\Primitive\DefaultFloatTypeProvider;
use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Provider\Primitive\DefaultPrimitiveBundle;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Provider\ProviderBundleInterface;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(DefaultPrimitiveBundle::class)]
#[CoversMethod(DefaultPrimitiveBundle::class, 'getProviders')]
#[CoversMethod(DefaultPrimitiveBundle::class, 'getTypeDefinitions')]
class DefaultPrimitiveBundleTest extends PhodamBaseTestCase
{
    public function testGetProvidersReturnsAllPrimitiveProviderClasses(): void
    {
        $bundle = new DefaultPrimitiveBundle();
        $providers = $bundle->getProviders();

        $this->assertIsArray($providers);
        $this->assertCount(4, $providers, 'Should return 4 primitive provider classes');

        $expectedProviders = [
            DefaultBoolTypeProvider::class,
            DefaultFloatTypeProvider::class,
            DefaultIntTypeProvider::class,
            DefaultStringTypeProvider::class,
        ];

        foreach ($expectedProviders as $expectedProvider) {
            $this->assertContains($expectedProvider, $providers, "Should contain {$expectedProvider}");
        }
    }

    public function testGetProvidersReturnsBoolProvider(): void
    {
        $bundle = new DefaultPrimitiveBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultBoolTypeProvider::class, $providers);
    }

    public function testGetProvidersReturnsFloatProvider(): void
    {
        $bundle = new DefaultPrimitiveBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultFloatTypeProvider::class, $providers);
    }

    public function testGetProvidersReturnsIntProvider(): void
    {
        $bundle = new DefaultPrimitiveBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultIntTypeProvider::class, $providers);
    }

    public function testGetProvidersReturnsStringProvider(): void
    {
        $bundle = new DefaultPrimitiveBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultStringTypeProvider::class, $providers);
    }

    public function testGetTypeDefinitionsReturnsEmptyArray(): void
    {
        $bundle = new DefaultPrimitiveBundle();
        $definitions = $bundle->getTypeDefinitions();

        $this->assertIsArray($definitions);
        $this->assertEmpty($definitions, 'DefaultPrimitiveBundle should not have any type definitions');
    }

    public function testImplementsProviderBundleInterface(): void
    {
        $bundle = new DefaultPrimitiveBundle();

        $this->assertInstanceOf(ProviderBundleInterface::class, $bundle);
    }
}
