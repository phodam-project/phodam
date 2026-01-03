<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use Phodam\Provider\Builtin\DefaultBuiltinBundle;
use Phodam\Provider\Builtin\DefaultDateIntervalTypeProvider;
use Phodam\Provider\Builtin\DefaultDatePeriodTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeZoneTypeProvider;
use Phodam\Provider\ProviderBundleInterface;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(DefaultBuiltinBundle::class)]
#[CoversMethod(DefaultBuiltinBundle::class, 'getProviders')]
#[CoversMethod(DefaultBuiltinBundle::class, 'getTypeDefinitions')]
class DefaultBuiltinBundleTest extends PhodamBaseTestCase
{
    public function testGetProvidersReturnsAllBuiltinProviderClasses(): void
    {
        $bundle = new DefaultBuiltinBundle();
        $providers = $bundle->getProviders();

        $this->assertIsArray($providers);
        $this->assertCount(5, $providers, 'Should return 5 builtin provider classes');

        $expectedProviders = [
            DefaultDateIntervalTypeProvider::class,
            DefaultDatePeriodTypeProvider::class,
            DefaultDateTimeTypeProvider::class,
            DefaultDateTimeImmutableTypeProvider::class,
            DefaultDateTimeZoneTypeProvider::class,
        ];

        foreach ($expectedProviders as $expectedProvider) {
            $this->assertContains($expectedProvider, $providers, "Should contain {$expectedProvider}");
        }
    }

    public function testGetProvidersReturnsDateIntervalProvider(): void
    {
        $bundle = new DefaultBuiltinBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultDateIntervalTypeProvider::class, $providers);
    }

    public function testGetProvidersReturnsDatePeriodProvider(): void
    {
        $bundle = new DefaultBuiltinBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultDatePeriodTypeProvider::class, $providers);
    }

    public function testGetProvidersReturnsDateTimeProvider(): void
    {
        $bundle = new DefaultBuiltinBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultDateTimeTypeProvider::class, $providers);
    }

    public function testGetProvidersReturnsDateTimeImmutableProvider(): void
    {
        $bundle = new DefaultBuiltinBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultDateTimeImmutableTypeProvider::class, $providers);
    }

    public function testGetProvidersReturnsDateTimeZoneProvider(): void
    {
        $bundle = new DefaultBuiltinBundle();
        $providers = $bundle->getProviders();

        $this->assertContains(DefaultDateTimeZoneTypeProvider::class, $providers);
    }

    public function testGetTypeDefinitionsReturnsEmptyArray(): void
    {
        $bundle = new DefaultBuiltinBundle();
        $definitions = $bundle->getTypeDefinitions();

        $this->assertIsArray($definitions);
        $this->assertEmpty($definitions, 'DefaultBuiltinBundle should not have any type definitions');
    }

    public function testImplementsProviderBundleInterface(): void
    {
        $bundle = new DefaultBuiltinBundle();

        $this->assertInstanceOf(ProviderBundleInterface::class, $bundle);
    }
}
