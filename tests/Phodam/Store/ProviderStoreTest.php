<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Store;

use Phodam\Provider\ProviderInterface;
use Phodam\Store\ProviderConflictException;
use Phodam\Store\ProviderNotFoundException;
use Phodam\Store\ProviderStore;
use PhodamTests\Fixtures\SampleArrayProvider;
use PhodamTests\Fixtures\SampleProvider;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(\Phodam\Store\ProviderStore::class)]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'hasNamedProvider')]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'hasDefaultProvider')]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'findNamedProvider')]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'findDefaultProvider')]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'findAllNamedProvidersForType')]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'registerNamedProvider')]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'registerDefaultProvider')]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'deregisterNamedProvider')]
#[CoversMethod(\Phodam\Store\ProviderStore::class, 'deregisterDefaultProvider')]
class ProviderStoreTest extends PhodamBaseTestCase
{
    private ProviderStore $store;
    private ProviderInterface $provider1;
    private ProviderInterface $provider2;

    public function setUp(): void
    {
        parent::setUp();
        $this->store = new ProviderStore();
        $this->provider1 = new SampleProvider();
        $this->provider2 = new SampleArrayProvider();
    }

    public function testHasNamedProviderReturnsTrueWhenProviderExists(): void
    {
        $type = 'string';
        $name = 'myProvider';
        $this->store->registerNamedProvider($type, $name, $this->provider1);

        $this->assertTrue($this->store->hasNamedProvider($type, $name));
    }

    public function testHasNamedProviderReturnsFalseWhenProviderDoesNotExist(): void
    {
        $type = 'string';
        $name = 'myProvider';

        $this->assertFalse($this->store->hasNamedProvider($type, $name));
    }

    public function testHasNamedProviderReturnsFalseForDifferentName(): void
    {
        $type = 'string';
        $name1 = 'myProvider1';
        $name2 = 'myProvider2';
        $this->store->registerNamedProvider($type, $name1, $this->provider1);

        $this->assertFalse($this->store->hasNamedProvider($type, $name2));
    }

    public function testHasDefaultProviderReturnsTrueWhenProviderExists(): void
    {
        $type = 'string';
        $this->store->registerDefaultProvider($type, $this->provider1);

        $this->assertTrue($this->store->hasDefaultProvider($type));
    }

    public function testHasDefaultProviderReturnsFalseWhenProviderDoesNotExist(): void
    {
        $type = 'string';

        $this->assertFalse($this->store->hasDefaultProvider($type));
    }

    public function testFindNamedProviderReturnsProviderWhenItExists(): void
    {
        $type = 'string';
        $name = 'myProvider';
        $this->store->registerNamedProvider($type, $name, $this->provider1);

        $result = $this->store->findNamedProvider($type, $name);

        $this->assertSame($this->provider1, $result);
    }

    public function testFindNamedProviderThrowsExceptionWhenProviderDoesNotExist(): void
    {
        $type = 'string';
        $name = 'myProvider';

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No provider found for type {$type} with name {$name}");

        $this->store->findNamedProvider($type, $name);
    }

    public function testFindDefaultProviderReturnsProviderWhenItExists(): void
    {
        $type = 'string';
        $this->store->registerDefaultProvider($type, $this->provider1);

        $result = $this->store->findDefaultProvider($type);

        $this->assertSame($this->provider1, $result);
    }

    public function testFindDefaultProviderThrowsExceptionWhenProviderDoesNotExist(): void
    {
        $type = 'string';

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No default provider found for type {$type}");

        $this->store->findDefaultProvider($type);
    }

    public function testFindAllNamedProvidersForTypeReturnsArrayOfProviders(): void
    {
        $type = 'string';
        $name1 = 'provider1';
        $name2 = 'provider2';
        $this->store->registerNamedProvider($type, $name1, $this->provider1);
        $this->store->registerNamedProvider($type, $name2, $this->provider2);

        $result = $this->store->findAllNamedProvidersForType($type);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame($this->provider1, $result[$name1]);
        $this->assertSame($this->provider2, $result[$name2]);
    }

    public function testFindAllNamedProvidersForTypeReturnsEmptyArrayWhenNoProvidersExist(): void
    {
        $type = 'string';

        $result = $this->store->findAllNamedProvidersForType($type);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testRegisterNamedProviderSuccessfullyRegistersProvider(): void
    {
        $type = 'string';
        $name = 'myProvider';

        $this->store->registerNamedProvider($type, $name, $this->provider1);

        $this->assertTrue($this->store->hasNamedProvider($type, $name));
        $this->assertSame($this->provider1, $this->store->findNamedProvider($type, $name));
    }

    public function testRegisterNamedProviderThrowsExceptionWhenProviderAlreadyExists(): void
    {
        $type = 'string';
        $name = 'myProvider';
        $this->store->registerNamedProvider($type, $name, $this->provider1);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage("Provider for type {$type} with name {$name} already registered");

        $this->store->registerNamedProvider($type, $name, $this->provider2);
    }

    public function testRegisterDefaultProviderSuccessfullyRegistersProvider(): void
    {
        $type = 'string';

        $this->store->registerDefaultProvider($type, $this->provider1);

        $this->assertTrue($this->store->hasDefaultProvider($type));
        $this->assertSame($this->provider1, $this->store->findDefaultProvider($type));
    }

    public function testRegisterDefaultProviderThrowsExceptionWhenTryingToRegisterArrayType(): void
    {
        $type = 'array';

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage("Array providers must be registered with a name");

        $this->store->registerDefaultProvider($type, $this->provider1);
    }

    public function testRegisterDefaultProviderThrowsExceptionWhenProviderAlreadyExists(): void
    {
        $type = 'string';
        $this->store->registerDefaultProvider($type, $this->provider1);

        $this->expectException(ProviderConflictException::class);
        $this->expectExceptionMessage("Default provider for type {$type} already registered");

        $this->store->registerDefaultProvider($type, $this->provider2);
    }

    public function testDeregisterNamedProviderSuccessfullyRemovesProvider(): void
    {
        $type = 'string';
        $name = 'myProvider';
        $this->store->registerNamedProvider($type, $name, $this->provider1);

        $this->store->deregisterNamedProvider($type, $name);

        $this->assertFalse($this->store->hasNamedProvider($type, $name));
    }

    public function testDeregisterNamedProviderThrowsExceptionWhenProviderDoesNotExist(): void
    {
        $type = 'string';
        $name = 'myProvider';

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No provider found for type {$type} with name {$name}");

        $this->store->deregisterNamedProvider($type, $name);
    }

    public function testDeregisterDefaultProviderSuccessfullyRemovesProvider(): void
    {
        $type = 'string';
        $this->store->registerDefaultProvider($type, $this->provider1);

        $this->store->deregisterDefaultProvider($type);

        $this->assertFalse($this->store->hasDefaultProvider($type));
    }

    public function testDeregisterDefaultProviderThrowsExceptionWhenProviderDoesNotExist(): void
    {
        $type = 'string';

        $this->expectException(ProviderNotFoundException::class);
        $this->expectExceptionMessage("No default provider found for type {$type}");

        $this->store->deregisterDefaultProvider($type);
    }

    public function testTypeNormalizationBoolToBoolean(): void
    {
        $this->store->registerDefaultProvider('bool', $this->provider1);

        $this->assertTrue($this->store->hasDefaultProvider('bool'));
        $this->assertTrue($this->store->hasDefaultProvider('boolean'));
        $this->assertSame($this->provider1, $this->store->findDefaultProvider('bool'));
        $this->assertSame($this->provider1, $this->store->findDefaultProvider('boolean'));
    }

    public function testTypeNormalizationIntToInteger(): void
    {
        $this->store->registerDefaultProvider('int', $this->provider1);

        $this->assertTrue($this->store->hasDefaultProvider('int'));
        $this->assertTrue($this->store->hasDefaultProvider('integer'));
        $this->assertSame($this->provider1, $this->store->findDefaultProvider('int'));
        $this->assertSame($this->provider1, $this->store->findDefaultProvider('integer'));
    }

    public function testTypeNormalizationFloatToDouble(): void
    {
        $this->store->registerDefaultProvider('float', $this->provider1);

        $this->assertTrue($this->store->hasDefaultProvider('float'));
        $this->assertTrue($this->store->hasDefaultProvider('double'));
        $this->assertSame($this->provider1, $this->store->findDefaultProvider('float'));
        $this->assertSame($this->provider1, $this->store->findDefaultProvider('double'));
    }

    public function testTypeNormalizationForNamedProviders(): void
    {
        $name = 'myProvider';
        $this->store->registerNamedProvider('bool', $name, $this->provider1);

        $this->assertTrue($this->store->hasNamedProvider('bool', $name));
        $this->assertTrue($this->store->hasNamedProvider('boolean', $name));
        $this->assertSame($this->provider1, $this->store->findNamedProvider('bool', $name));
        $this->assertSame($this->provider1, $this->store->findNamedProvider('boolean', $name));
    }

    public function testMultipleNamedProvidersForSameType(): void
    {
        $type = 'string';
        $name1 = 'provider1';
        $name2 = 'provider2';
        $this->store->registerNamedProvider($type, $name1, $this->provider1);
        $this->store->registerNamedProvider($type, $name2, $this->provider2);

        $this->assertTrue($this->store->hasNamedProvider($type, $name1));
        $this->assertTrue($this->store->hasNamedProvider($type, $name2));
        $this->assertSame($this->provider1, $this->store->findNamedProvider($type, $name1));
        $this->assertSame($this->provider2, $this->store->findNamedProvider($type, $name2));

        $allProviders = $this->store->findAllNamedProvidersForType($type);
        $this->assertCount(2, $allProviders);
    }

    public function testDeregisterNamedProviderDoesNotAffectOtherProviders(): void
    {
        $type = 'string';
        $name1 = 'provider1';
        $name2 = 'provider2';
        $this->store->registerNamedProvider($type, $name1, $this->provider1);
        $this->store->registerNamedProvider($type, $name2, $this->provider2);

        $this->store->deregisterNamedProvider($type, $name1);

        $this->assertFalse($this->store->hasNamedProvider($type, $name1));
        $this->assertTrue($this->store->hasNamedProvider($type, $name2));
        $this->assertSame($this->provider2, $this->store->findNamedProvider($type, $name2));
    }
}

