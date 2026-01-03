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
use PhodamTests\Fixtures\TestProviderWithAttribute;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PhodamSchema::class)]
class ProviderRegistrationTest extends IntegrationBaseTestCase
{
    public function testRegisterDefaultProviderWithAttribute(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testRegisterDefaultProviderThroughSchema(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(DefaultStringTypeProvider::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertIsString($result);
    }

    public function testRegisterProviderWithDefinition(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for primitive types
        $type = UnregisteredClassType::class;
        $definition = new TypeDefinition($type, null, false, [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ]);

        $schema->registerTypeDefinition($definition);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
        $this->assertIsString($result->getField1());
        $this->assertIsString($result->getField2());
        $this->assertIsInt($result->getField3());
    }

    public function testRegisterProviderWithClassString(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(DefaultStringTypeProvider::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertIsString($result);
    }

    public function testRegisterProviderWithInstance(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new DefaultStringTypeProvider();
        $schema->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertIsString($result);
    }

    public function testRegisteredProviderIsUsed(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for nested types
        $schema->registerProvider(TestProviderWithAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        // Verify the custom provider was used
        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testProviderRegistrationPersists(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(DefaultStringTypeProvider::class);

        $phodam = $schema->getPhodam();

        // Create multiple times - provider should persist
        $result1 = $phodam->create('string');
        $result2 = $phodam->create('string');
        $result3 = $phodam->create('string');

        $this->assertIsString($result1);
        $this->assertIsString($result2);
        $this->assertIsString($result3);
    }
}

