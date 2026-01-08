<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Integration\ProviderManagement;

use Phodam\Phodam;
use Phodam\PhodamSchema;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use PhodamTests\Fixtures\TestNamedProviderWithAttribute;
use PhodamTests\Fixtures\TestProviderWithAttribute;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Phodam::class)]
class NamedProviderTest extends IntegrationBaseTestCase
{
    public function testRegisterNamedProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestNamedProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'customProvider');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testCreateWithNamedProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestNamedProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'customProvider');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testMultipleNamedProvidersForSameType(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        
        // Create additional named providers
        $provider1 = new class extends TestProviderWithAttribute {};
        $provider2 = new class extends TestProviderWithAttribute {};
        
        // We can't dynamically add attributes, so we'll use TypeDefinition for additional named providers
        $type = UnregisteredClassType::class;
        $definition1 = new TypeDefinition($type, 'provider1', false, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);
        $definition2 = new TypeDefinition($type, 'provider2', false, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);

        $schema->registerProvider(TestNamedProviderWithAttribute::class);
        $schema->registerTypeDefinition($definition1);
        $schema->registerTypeDefinition($definition2);

        $phodam = $schema->getPhodam();

        $result1 = $phodam->create(UnregisteredClassType::class, 'provider1');
        $result2 = $phodam->create(UnregisteredClassType::class, 'provider2');

        $this->assertInstanceOf(UnregisteredClassType::class, $result1);
        $this->assertInstanceOf(UnregisteredClassType::class, $result2);
    }

    public function testNamedProviderDoesNotAffectDefault(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types

        // Register default provider
        $schema->registerProvider(TestProviderWithAttribute::class);

        // Register named provider using TypeDefinition
        $type = UnregisteredClassType::class;
        $definition = new TypeDefinition($type, 'named', false, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);
        $schema->registerTypeDefinition($definition);

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
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types

        // Register default provider
        $schema->registerProvider(TestProviderWithAttribute::class);

        // Register named providers using TypeDefinition
        $type = UnregisteredClassType::class;
        $definition1 = new TypeDefinition($type, 'named1', false, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);
        $definition2 = new TypeDefinition($type, 'named2', false, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);
        $schema->registerTypeDefinition($definition1);
        $schema->registerTypeDefinition($definition2);

        $phodam = $schema->getPhodam();

        // Default should still work
        $result = $phodam->create(UnregisteredClassType::class);
        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testNamedProviderOverrides(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestNamedProviderWithAttribute::class);

        $phodam = $schema->getPhodam();

        $overrides = ['field1' => 'custom value'];
        $result = $phodam->create(UnregisteredClassType::class, 'customProvider', $overrides);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertEquals('custom value', $result->getField1());
    }
}

