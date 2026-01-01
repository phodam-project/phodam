<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ProviderManagement;

use Phodam\PhodamSchema;
use Phodam\Store\ProviderConflictException;
use PhodamTests\Fixtures\SampleProvider;
use PhodamTests\Fixtures\UnregisteredClassType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Store\ProviderStore::class)]
class ProviderConflictTest extends IntegrationBaseTestCase
{
    public function testRegisterDuplicateDefaultProviderThrowsException(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();

        $schema->forType('string')
            ->registerProvider($provider);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage('already registered');

        $schema->forType('string')
            ->registerProvider($provider);
    }

    public function testRegisterDuplicateNamedProviderThrowsException(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new \PhodamTests\Fixtures\SampleArrayProvider();

        $schema->forType('array')
            ->withName('myProvider')
            ->registerProvider($provider);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage('already registered');

        $schema->forType('array')
            ->withName('myProvider')
            ->registerProvider($provider);
    }

    public function testRegisterArrayAsDefaultThrowsException(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new \PhodamTests\Fixtures\SampleArrayProvider();

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage('Array providers must be registered with a name');

        $schema->forType('array')
            ->registerProvider($provider);
    }

    public function testExceptionMessagesAreDescriptive(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();

        $schema->forType('string')
            ->registerProvider($provider);

        try {
            $schema->forType('string')
                ->registerProvider($provider);
            $this->fail('Expected ProviderConflictException');
        } catch (ProviderConflictException $e) {
            $this->assertStringContainsString('already registered', $e->getMessage());
            $this->assertStringContainsString('string', $e->getMessage());
        }
    }

    public function testOverridePreventsConflict(): void
    {
        $schema = PhodamSchema::blank();
        $provider1 = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();
        $provider2 = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();

        $schema->forType('string')
            ->registerProvider($provider1);

        // Using overriding() should prevent conflict
        $schema->forType('string')
            ->overriding()
            ->registerProvider($provider2);

        $phodam = $schema->getPhodam();
        $result = $phodam->create('string');

        $this->assertIsString($result);
    }
}

