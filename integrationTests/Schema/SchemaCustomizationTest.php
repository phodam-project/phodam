<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\Schema;

use Phodam\PhodamSchema;
use PhodamTests\Fixtures\SampleProviderBundle;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\PhodamSchema::class)]
class SchemaCustomizationTest extends IntegrationBaseTestCase
{
    public function testAddCustomProviderBundle(): void
    {
        $schema = PhodamSchema::blank();
        $bundle = new SampleProviderBundle();

        $schema->add($bundle);

        // Should not throw exception
        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testAddProviderBundleByClassString(): void
    {
        $schema = PhodamSchema::blank();

        $schema->add(SampleProviderBundle::class);

        // Should not throw exception
        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testAddProviderBundleByInstance(): void
    {
        $schema = PhodamSchema::blank();
        $bundle = new SampleProviderBundle();

        $schema->add($bundle);

        // Should not throw exception
        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testCustomProviderOverridesDefault(): void
    {
        $schema = PhodamSchema::withDefaults();
        $customProvider = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();

        // Register custom provider - it should override default
        $schema->forType('string')
            ->overriding()
            ->registerProvider($customProvider);

        $phodam = $schema->getPhodam();
        $stringValue = $phodam->create('string');

        $this->assertIsString($stringValue);
    }

    public function testMultipleBundlesCanBeAdded(): void
    {
        $schema = PhodamSchema::blank();

        $schema->add(SampleProviderBundle::class);
        $schema->add(\Phodam\Provider\DefaultProviderBundle::class);

        // Should not throw exception
        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }
}

