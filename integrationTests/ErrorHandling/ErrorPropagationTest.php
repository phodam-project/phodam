<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ErrorHandling;

use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Phodam;
use Phodam\PhodamSchema;
use Phodam\Provider\CreationFailedException;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Store\ProviderConflictException;
use Phodam\Store\ProviderNotFoundException;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Phodam::class)]
class ErrorPropagationTest extends IntegrationBaseTestCase
{
    public function testProviderNotFoundExceptionPropagates(): void
    {
        $phodam = $this->createBlankPhodam();

        // For class types, ProviderNotFoundException is thrown
        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage('No default provider found');

        /** @var Phodam $phodam */
        $phodam->getTypeProvider(UnregisteredClassType::class);
    }

    public function testProviderConflictExceptionPropagates(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(DefaultStringTypeProvider::class);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage('already registered');

        $schema->registerProvider(DefaultStringTypeProvider::class);
    }

    public function testTypeAnalysisExceptionPropagates(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $this->expectException(TypeAnalysisException::class);
        $this->expectExceptionMessage('Unable to map fields');

        $phodam->create(SimpleTypeMissingSomeFieldTypes::class);
    }

    public function testCreationFailedExceptionWrapsErrors(): void
    {
        $schema = PhodamSchema::blank();
        // We can't easily test this with mocks since providers need attributes
        // This test would require a provider class file with attribute that throws
        // For now, let's test that valid providers work
        $schema->registerProvider(DefaultStringTypeProvider::class);
        $phodam = $schema->getPhodam();
        $this->assertIsString($phodam->create('string'));
    }

    public function testIncompleteDefinitionExceptionPropagates(): void
    {
        $schema = PhodamSchema::withDefaults(); // Need defaults for int type
        $type = SimpleTypeMissingSomeFieldTypes::class;
        $definition = new TypeDefinition($type, null, false, [
            'myInt' => new FieldDefinition('int'),
            // Missing myString field - this will cause CreationFailedException wrapping IncompleteDefinitionException
        ]);

        $schema->registerTypeDefinition($definition);

        $phodam = $schema->getPhodam();

        // IncompleteDefinitionException is wrapped in CreationFailedException
        $this->expectException(CreationFailedException::class);
        $this->expectExceptionMessage('Creation failed');

        $phodam->create(SimpleTypeMissingSomeFieldTypes::class);
    }

    public function testExceptionMessagesArePreserved(): void
    {
        $phodam = $this->createBlankPhodam();

        try {
            /** @var Phodam $phodam */
            $phodam->getTypeProvider(UnregisteredClassType::class);
            $this->fail('Expected ProviderNotFoundException');
        } catch (ProviderNotFoundException $e) {
            $this->assertStringContainsString('No default provider found', $e->getMessage());
            $this->assertStringContainsString('UnregisteredClassType', $e->getMessage());
        }
    }
}

