<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Integration\ProviderManagement;

use Phodam\PhodamSchema;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use PhodamTests\Fixtures\TestArrayProviderWithAttribute;
use PhodamTests\Fixtures\TestNamedArrayProviderWithAttribute;
use PhodamTests\Fixtures\TestNamedProviderWithAttribute;
use PhodamTests\Fixtures\TestProviderWithAttribute;
use PhodamTests\Fixtures\TestProviderWithOverridingAttribute;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PhodamSchema::class)]
class AttributeBasedRegistrationTest extends IntegrationBaseTestCase
{
    public function testRegisterProviderWithPhodamProviderAttribute(): void
    {
        $schema = PhodamSchema::withDefaults();
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testRegisterProviderWithPhodamProviderAttributeAndName(): void
    {
        $schema = PhodamSchema::withDefaults();
        $schema->registerProvider(TestNamedProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'customProvider');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testRegisterProviderWithPhodamArrayProviderAttribute(): void
    {
        $schema = PhodamSchema::withDefaults();
        $schema->registerProvider(TestArrayProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->createArray('testArray');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('field1', $result);
    }

    public function testRegisterProviderWithOverridingAttribute(): void
    {
        $schema = PhodamSchema::blank();
        
        // Register default provider first
        $schema->registerProvider(DefaultStringTypeProvider::class);
        
        // Register provider with overriding attribute - should override the existing one
        $schema->registerProvider(TestProviderWithOverridingAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertEquals('custom value', $result);
    }

    public function testRegisterProviderWithInstance(): void
    {
        $schema = PhodamSchema::withDefaults();
        $provider = new TestProviderWithAttribute();
        $schema->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testRegisterProviderWithClassString(): void
    {
        $schema = PhodamSchema::withDefaults();
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testRegisterMultipleArrayProviders(): void
    {
        $schema = PhodamSchema::withDefaults();
        $schema->registerProvider(TestArrayProviderWithAttribute::class);
        $schema->registerProvider(TestNamedArrayProviderWithAttribute::class);

        $phodam = $schema->getPhodam();

        $result1 = $phodam->createArray('testArray');
        $result2 = $phodam->createArray('array1');

        $this->assertIsArray($result1);
        $this->assertIsArray($result2);
    }

    public function testRegisterTypeDefinitionWithName(): void
    {
        $schema = PhodamSchema::withDefaults();
        $type = UnregisteredClassType::class;
        $definition = new TypeDefinition($type, 'customDef', false, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);

        $schema->registerTypeDefinition($definition);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class, 'customDef');

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testRegisterTypeDefinitionWithOverriding(): void
    {
        $schema = PhodamSchema::withDefaults();
        $type = UnregisteredClassType::class;

        // Register first definition
        $definition1 = new TypeDefinition($type, null, false, [
            'field1' => new FieldDefinition('string'),
        ]);
        $schema->registerTypeDefinition($definition1);

        // Override with second definition
        $definition2 = new TypeDefinition($type, null, true, [
            'field1' => new FieldDefinition('int'),
        ]);
        $schema->registerTypeDefinition($definition2);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }
}
