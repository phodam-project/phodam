<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ObjectCreation;

use PhodamTests\Fixtures\SampleArrayProvider;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Phodam::class)]
class ArrayCreationTest extends IntegrationBaseTestCase
{
    public function testCreateNamedArray(): void
    {
        $schema = \Phodam\PhodamSchema::withDefaults(); // Need defaults for SampleArrayProvider
        $provider = new SampleArrayProvider();

        $schema->forType('array')
            ->withName('testArray')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->createArray('testArray');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('field1', $result);
        $this->assertArrayHasKey('field2', $result);
        $this->assertArrayHasKey('field3', $result);
    }

    public function testCreateArrayWithOverrides(): void
    {
        $schema = \Phodam\PhodamSchema::withDefaults(); // Need defaults for SampleArrayProvider
        $provider = new SampleArrayProvider();

        $schema->forType('array')
            ->withName('testArray')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $overrides = ['field1' => 'overridden value'];
        $result = $phodam->createArray('testArray', $overrides);

        $this->assertIsArray($result);
        $this->assertEquals('overridden value', $result['field1']);
    }

    public function testCreateArrayWithConfig(): void
    {
        $schema = \Phodam\PhodamSchema::withDefaults(); // Need defaults for SampleArrayProvider
        $provider = new SampleArrayProvider();

        $schema->forType('array')
            ->withName('testArray')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $config = ['someConfig' => 'value'];
        $result = $phodam->createArray('testArray', null, $config);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('field1', $result);
        $this->assertArrayHasKey('field2', $result);
        $this->assertArrayHasKey('field3', $result);
        // Config is passed to provider but SampleArrayProvider doesn't use it
        // This test just verifies config can be passed without errors
    }

    public function testArrayProviderIsRegistered(): void
    {
        $schema = \Phodam\PhodamSchema::withDefaults(); // Need defaults for SampleArrayProvider
        $provider = new SampleArrayProvider();

        $schema->forType('array')
            ->withName('testArray')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->createArray('testArray');

        $this->assertIsArray($result);
    }

    public function testMultipleArrayProviders(): void
    {
        $schema = \Phodam\PhodamSchema::withDefaults(); // Need defaults for SampleArrayProvider
        $provider1 = new SampleArrayProvider();
        $provider2 = new SampleArrayProvider();

        $schema->forType('array')
            ->withName('array1')
            ->registerProvider($provider1);

        $schema->forType('array')
            ->withName('array2')
            ->registerProvider($provider2);

        $phodam = $schema->getPhodam();

        $result1 = $phodam->createArray('array1');
        $result2 = $phodam->createArray('array2');

        $this->assertIsArray($result1);
        $this->assertIsArray($result2);
    }
}

