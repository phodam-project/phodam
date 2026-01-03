<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\Advanced;

use Phodam\Phodam;
use Phodam\PhodamSchema;
use Phodam\Provider\ProviderContext;
use PhodamTests\Fixtures\TestNamedProviderWithAttribute;
use PhodamTests\Fixtures\TestProviderWithAttribute;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Phodam::class)]
#[CoversClass(ProviderContext::class)]
class CustomProviderIntegrationTest extends IntegrationBaseTestCase
{
    public function testCustomProviderIsUsed(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testCustomProviderReceivesContext(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        // Provider receives context and uses it to create nested objects
        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testCustomProviderCanUseOverrides(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $overrides = ['field1' => 'custom value'];
        $result = $phodam->create(UnregisteredClassType::class, null, $overrides);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertEquals('custom value', $result->getField1());
    }

    public function testCustomProviderCanUseConfig(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $config = ['minYear' => 1990, 'maxYear' => 2000];
        $result = $phodam->create(UnregisteredClassType::class, null, null, $config);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertGreaterThanOrEqual(1990, $result->getField3());
        $this->assertLessThanOrEqual(2000, $result->getField3());
    }

    public function testCustomProviderCanCreateNestedObjects(): void
    {
        $schema = PhodamSchema::withDefaults();
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertIsString($result->getField1());
        $this->assertIsString($result->getField2());
        $this->assertIsInt($result->getField3());
    }

    public function testCustomProviderWithNamedProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestNamedProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'customProvider');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }
}

