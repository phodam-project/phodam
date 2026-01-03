<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ObjectCreation;

use Phodam\Phodam;
use Phodam\PhodamSchema;
use PhodamTests\Fixtures\TestArrayProviderWithAttribute;
use PhodamTests\Fixtures\TestNamedArrayProviderWithAttribute;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Phodam::class)]
class ArrayCreationTest extends IntegrationBaseTestCase
{
    public function testCreateNamedArray(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestArrayProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->createArray('testArray');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('field1', $result);
        $this->assertArrayHasKey('field2', $result);
        $this->assertArrayHasKey('field3', $result);
    }

    public function testCreateArrayWithOverrides(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestArrayProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $overrides = ['field1' => 'overridden value'];
        $result = $phodam->createArray('testArray', $overrides);

        $this->assertIsArray($result);
        $this->assertEquals('overridden value', $result['field1']);
    }

    public function testCreateArrayWithConfig(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestArrayProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $config = ['someConfig' => 'value'];
        $result = $phodam->createArray('testArray', null, $config);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('field1', $result);
        $this->assertArrayHasKey('field2', $result);
        $this->assertArrayHasKey('field3', $result);
        // Config is passed to provider but TestArrayProviderWithAttribute doesn't use it
        // This test just verifies config can be passed without errors
    }

    public function testArrayProviderIsRegistered(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestArrayProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->createArray('testArray');

        $this->assertIsArray($result);
    }

    public function testMultipleArrayProviders(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestArrayProviderWithAttribute::class);
        $schema->registerProvider(TestNamedArrayProviderWithAttribute::class);

        $phodam = $schema->getPhodam();

        $result1 = $phodam->createArray('testArray');
        $result2 = $phodam->createArray('array1');

        $this->assertIsArray($result1);
        $this->assertIsArray($result2);
    }
}

