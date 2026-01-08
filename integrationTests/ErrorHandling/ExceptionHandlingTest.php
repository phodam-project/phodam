<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Integration\ErrorHandling;

use InvalidArgumentException;
use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Phodam;
use Phodam\PhodamSchema;
use Phodam\Provider\CreationFailedException;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Store\ProviderNotFoundException;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\TestProviderThatThrowsException;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionException;

#[CoversClass(Phodam::class)]
class ExceptionHandlingTest extends IntegrationBaseTestCase
{
    public function testInvalidTypeThrowsException(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Non-existent class should throw exception
        $this->expectException(ReflectionException::class);

        $phodam->create('NonExistentClass' . uniqid());
    }

    public function testMissingProviderThrowsException(): void
    {
        $phodam = $this->createBlankPhodam();

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage('No default provider found');

        /** @var Phodam $phodam */
        $phodam->getTypeProvider(UnregisteredClassType::class);
    }

    public function testUnmappableFieldThrowsException(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $this->expectException(TypeAnalysisException::class);
        $this->expectExceptionMessage('Unable to map fields');

        $phodam->create(SimpleTypeMissingSomeFieldTypes::class);
    }

    public function testInvalidProviderClassThrowsException(): void
    {
        $schema = PhodamSchema::blank();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must have a PhodamProvider or PhodamArrayProvider attribute');

        // Create a provider without attribute
        $provider = new class implements ProviderInterface {
            public function create(ProviderContextInterface $context)
            {
                return 'test';
            }
        };

        $schema->registerProvider($provider);
    }

    public function testExceptionDuringCreationIsWrapped(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(TestProviderThatThrowsException::class);

        $phodam = $schema->getPhodam();

        $this->expectException(CreationFailedException::class);
        $this->expectExceptionMessage('Creation failed');

        try {
            $phodam->create('string');
        } catch (CreationFailedException $e) {
            $this->assertStringContainsString('string', $e->getMessage());
            throw $e;
        }
    }
}

