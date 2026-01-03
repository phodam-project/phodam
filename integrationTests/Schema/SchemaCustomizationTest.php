<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\Schema;

use Phodam\PhodamSchema;
use Phodam\Provider\Builtin\DefaultBuiltinBundle;
use Phodam\Provider\Primitive\DefaultPrimitiveBundle;
use PhodamTests\Fixtures\SampleProviderBundle;
use PhodamTests\Fixtures\TestProviderWithOverridingAttribute;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PhodamSchema::class)]
class SchemaCustomizationTest extends IntegrationBaseTestCase
{
    public function testRegisterCustomProviderBundle(): void
    {
        $schema = PhodamSchema::blank();
        $bundle = new SampleProviderBundle();

        $schema->registerBundle($bundle);

        // Should not throw exception
        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testRegisterProviderBundleByClassString(): void
    {
        $schema = PhodamSchema::blank();

        $schema->registerBundle(SampleProviderBundle::class);

        // Should not throw exception
        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testRegisterProviderBundleByInstance(): void
    {
        $schema = PhodamSchema::blank();
        $bundle = new SampleProviderBundle();

        $schema->registerBundle($bundle);

        // Should not throw exception
        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }

    public function testCustomProviderOverridesDefault(): void
    {
        $schema = PhodamSchema::withDefaults();
        
        // Register custom provider with overriding attribute - it should override default
        $schema->registerProvider(TestProviderWithOverridingAttribute::class);

        $phodam = $schema->getPhodam();
        $stringValue = $phodam->create('string');

        $this->assertIsString($stringValue);
        $this->assertEquals('custom value', $stringValue);
    }

    public function testMultipleBundlesCanBeRegistered(): void
    {
        $schema = PhodamSchema::blank();

        $schema->registerBundle(SampleProviderBundle::class);
        $schema->registerBundle(DefaultPrimitiveBundle::class);
        $schema->registerBundle(DefaultBuiltinBundle::class);

        // Should not throw exception
        $this->assertInstanceOf(PhodamSchema::class, $schema);
    }
}

