<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ErrorHandling;

use Phodam\Provider\CreationFailedException;
use Phodam\Provider\IncompleteDefinitionException;
use Phodam\Store\ProviderConflictException;
use Phodam\Store\ProviderNotFoundException;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Phodam::class)]
class ErrorPropagationTest extends IntegrationBaseTestCase
{
    public function testProviderNotFoundExceptionPropagates(): void
    {
        $phodam = $this->createBlankPhodam();

        // For class types, ProviderNotFoundException is thrown
        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage('No default provider found');

        /** @var \Phodam\Phodam $phodam */
        $phodam->getTypeProvider(\PhodamTests\Fixtures\UnregisteredClassType::class);
    }

    public function testProviderConflictExceptionPropagates(): void
    {
        $schema = \Phodam\PhodamSchema::blank();
        $provider = new \PhodamTests\Fixtures\SampleProvider();

        $schema->forType('string')
            ->registerProvider($provider);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage('already registered');

        $schema->forType('string')
            ->registerProvider($provider);
    }

    public function testTypeAnalysisExceptionPropagates(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $this->expectException(\Phodam\Analyzer\TypeAnalysisException::class);
        $this->expectExceptionMessage('Unable to map fields');

        $phodam->create(SimpleTypeMissingSomeFieldTypes::class);
    }

    public function testCreationFailedExceptionWrapsErrors(): void
    {
        $schema = \Phodam\PhodamSchema::blank();
        $provider = $this->createMock(\Phodam\Provider\ProviderInterface::class);
        $provider->method('create')
            ->willThrowException(new \RuntimeException('Provider error'));

        $schema->forType('string')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();

        $this->expectException(CreationFailedException::class);
        $this->expectExceptionMessage('Creation failed');

        $phodam->create('string');
    }

    public function testIncompleteDefinitionExceptionPropagates(): void
    {
        $schema = \Phodam\PhodamSchema::withDefaults(); // Need defaults for int type
        $definition = new \Phodam\Types\TypeDefinition([
            'myInt' => new \Phodam\Types\FieldDefinition('int'),
            // Missing myString field - this will cause CreationFailedException wrapping IncompleteDefinitionException
        ]);

        $schema->forType(SimpleTypeMissingSomeFieldTypes::class)
            ->registerDefinition($definition);

        $phodam = $schema->getPhodam();

        // IncompleteDefinitionException is wrapped in CreationFailedException
        $this->expectException(\Phodam\Provider\CreationFailedException::class);
        $this->expectExceptionMessage('Creation failed');

        $phodam->create(SimpleTypeMissingSomeFieldTypes::class);
    }

    public function testExceptionMessagesArePreserved(): void
    {
        $phodam = $this->createBlankPhodam();

        try {
            /** @var \Phodam\Phodam $phodam */
            $phodam->getTypeProvider(\PhodamTests\Fixtures\UnregisteredClassType::class);
            $this->fail('Expected ProviderNotFoundException');
        } catch (ProviderNotFoundException $e) {
            $this->assertStringContainsString('No default provider found', $e->getMessage());
            $this->assertStringContainsString('UnregisteredClassType', $e->getMessage());
        }
    }
}

