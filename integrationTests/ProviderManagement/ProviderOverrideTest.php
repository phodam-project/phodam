<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ProviderManagement;

use Phodam\PhodamSchema;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use PhodamTests\Fixtures\TestNamedProviderWithAttribute;
use PhodamTests\Fixtures\TestProviderWithAttribute;
use PhodamTests\Fixtures\TestProviderWithOverridingAttribute;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PhodamSchema::class)]
class ProviderOverrideTest extends IntegrationBaseTestCase
{
    public function testOverrideDefaultProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types

        // Register first provider
        $schema->registerProvider(TestProviderWithAttribute::class);

        // Override with second provider using TypeDefinition with overriding flag
        $type = UnregisteredClassType::class;
        $definition = new TypeDefinition($type, null, true, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);
        $schema->registerTypeDefinition($definition);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testOverrideNamedProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types

        // Register first named provider
        $schema->registerProvider(TestNamedProviderWithAttribute::class);

        // Override with second provider using TypeDefinition
        $type = UnregisteredClassType::class;
        $definition = new TypeDefinition($type, 'customProvider', true, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);
        $schema->registerTypeDefinition($definition);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'customProvider');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testOverrideReplacesExistingProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types

        $schema->registerProvider(TestProviderWithAttribute::class);

        // Override with TypeDefinition
        $type = UnregisteredClassType::class;
        $definition = new TypeDefinition($type, null, true, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);
        $schema->registerTypeDefinition($definition);

        $phodam = $schema->getPhodam();

        // Should use the new provider
        $result = $phodam->create(UnregisteredClassType::class);
        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testOverrideWithDifferentBehavior(): void
    {
        $schema = PhodamSchema::blank();

        // Register default string provider
        $schema->registerProvider(DefaultStringTypeProvider::class);

        // Override with a provider that has overriding attribute
        $schema->registerProvider(TestProviderWithOverridingAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertEquals('custom value', $result);
    }

    public function testOverridePreservesType(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types

        $schema->registerProvider(TestProviderWithAttribute::class);

        // Override with TypeDefinition
        $type = UnregisteredClassType::class;
        $definition = new TypeDefinition($type, null, true, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);
        $schema->registerTypeDefinition($definition);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }
}

