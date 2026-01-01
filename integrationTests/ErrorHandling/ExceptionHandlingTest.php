<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ErrorHandling;

use Phodam\Provider\CreationFailedException;
use Phodam\Store\ProviderNotFoundException;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Phodam::class)]
class ExceptionHandlingTest extends IntegrationBaseTestCase
{
    public function testInvalidTypeThrowsException(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Non-existent class should throw exception
        $this->expectException(\ReflectionException::class);

        $phodam->create('NonExistentClass' . uniqid());
    }

    public function testMissingProviderThrowsException(): void
    {
        $phodam = $this->createBlankPhodam();

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage('No default provider found');

        /** @var \Phodam\Phodam $phodam */
        $phodam->getTypeProvider(\PhodamTests\Fixtures\UnregisteredClassType::class);
    }

    public function testUnmappableFieldThrowsException(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $this->expectException(\Phodam\Analyzer\TypeAnalysisException::class);
        $this->expectExceptionMessage('Unable to map fields');

        $phodam->create(SimpleTypeMissingSomeFieldTypes::class);
    }

    public function testInvalidProviderClassThrowsException(): void
    {
        $schema = \Phodam\PhodamSchema::blank();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be an instance of ProviderInterface');

        $schema->forType('string')
            ->registerProvider(\stdClass::class);
    }

    public function testExceptionDuringCreationIsWrapped(): void
    {
        $schema = \Phodam\PhodamSchema::blank();
        $provider = $this->createMock(\Phodam\Provider\ProviderInterface::class);
        $provider->method('create')
            ->willThrowException(new \RuntimeException('Internal error'));

        $schema->forType('string')
            ->registerProvider($provider);

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

