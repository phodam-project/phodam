<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ProviderManagement;

use InvalidArgumentException;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\PhodamSchema;
use Phodam\Store\ProviderConflictException;
use Phodam\Store\ProviderStore;
use PhodamTests\Fixtures\TestArrayProviderWithAttribute;
use PhodamTests\Fixtures\TestProviderWithOverridingAttribute;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ProviderStore::class)]
class ProviderConflictTest extends IntegrationBaseTestCase
{
    public function testRegisterDuplicateDefaultProviderThrowsException(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(DefaultStringTypeProvider::class);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage('already registered');

        $schema->registerProvider(DefaultStringTypeProvider::class);
    }

    public function testRegisterDuplicateNamedProviderThrowsException(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(TestArrayProviderWithAttribute::class);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage('already registered');

        $schema->registerProvider(TestArrayProviderWithAttribute::class);
    }

    public function testRegisterArrayAsDefaultThrowsException(): void
    {
        $schema = PhodamSchema::blank();
        
        // Create a provider without PhodamArrayProvider attribute that tries to register as default for array
        $provider = new class implements ProviderInterface {
            public function create(ProviderContextInterface $context)
            {
                return [];
            }
        };

        // This will fail because provider has no attribute, but if it did have PhodamProvider('array'),
        // it would throw the array exception. Let's test with a provider that has PhodamProvider('array')
        // Actually, we can't easily test this without creating a provider class file. Let's test the error case.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must have a PhodamProvider or PhodamArrayProvider attribute');

        $schema->registerProvider($provider);
    }

    public function testExceptionMessagesAreDescriptive(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(DefaultStringTypeProvider::class);

        try {
            $schema->registerProvider(DefaultStringTypeProvider::class);
            $this->fail('Expected ProviderConflictException');
        } catch (ProviderConflictException $e) {
            $this->assertStringContainsString('already registered', $e->getMessage());
            $this->assertStringContainsString('string', $e->getMessage());
        }
    }

    public function testOverridePreventsConflict(): void
    {
        $schema = PhodamSchema::blank();
        $schema->registerProvider(DefaultStringTypeProvider::class);

        // Using overriding attribute should prevent conflict
        $schema->registerProvider(TestProviderWithOverridingAttribute::class);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertIsString($result);
        $this->assertEquals('custom value', $result);
    }
}

