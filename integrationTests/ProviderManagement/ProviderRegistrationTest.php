<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ProviderManagement;

use Phodam\PhodamSchema;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use PhodamTests\Fixtures\SampleProvider;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\PhodamSchema::class)]
#[CoversClass(\Phodam\Store\Registrar::class)]
class ProviderRegistrationTest extends IntegrationBaseTestCase
{
    public function testRegisterDefaultProvider(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testRegisterDefaultProviderThroughSchema(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();

        $schema->forType('string')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertIsString($result);
    }

    public function testRegisterProviderWithDefinition(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for primitive types
        $fields = [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int'),
        ];
        $definition = new TypeDefinition($fields);

        $schema->forType(UnregisteredClassType::class)
            ->registerDefinition($definition);

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

        $schema->forType('string')
            ->registerProvider(\Phodam\Provider\Primitive\DefaultStringTypeProvider::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertIsString($result);
    }

    public function testRegisterProviderWithInstance(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();

        $schema->forType('string')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertIsString($result);
    }

    public function testRegisteredProviderIsUsed(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for SampleProvider to work
        $provider = new SampleProvider();

        $schema->forType(UnregisteredClassType::class)
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $result = $phodam->create(UnregisteredClassType::class);

        // Verify the custom provider was used
        $this->assertInstanceOf(UnregisteredClassType::class, $result);
    }

    public function testProviderRegistrationPersists(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();

        $schema->forType('string')
            ->registerProvider($provider);

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

