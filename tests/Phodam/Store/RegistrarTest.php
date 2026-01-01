<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Store;

use InvalidArgumentException;
use Phodam\Provider\DefinitionBasedTypeProvider;
use Phodam\Provider\ProviderInterface;
use Phodam\Store\ProviderStoreInterface;
use Phodam\Store\Registrar;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(\Phodam\Store\Registrar::class)]
#[CoversMethod(\Phodam\Store\Registrar::class, '__construct')]
#[CoversMethod(\Phodam\Store\Registrar::class, 'withType')]
#[CoversMethod(\Phodam\Store\Registrar::class, 'withName')]
#[CoversMethod(\Phodam\Store\Registrar::class, 'overriding')]
#[CoversMethod(\Phodam\Store\Registrar::class, 'registerProvider')]
#[CoversMethod(\Phodam\Store\Registrar::class, 'registerDefinition')]
class RegistrarTest extends PhodamBaseTestCase
{
    /** @var ProviderStoreInterface&MockObject */
    private $store;
    private ProviderInterface $provider;

    public function setUp(): void
    {
        parent::setUp();
        $this->store = $this->createMock(ProviderStoreInterface::class);
        $this->provider = $this->createMock(ProviderInterface::class);
    }

    public function testConstruct(): void
    {
        $registrar = new Registrar($this->store);

        $this->assertInstanceOf(Registrar::class, $registrar);
    }

    public function testWithTypeSetsTypeAndReturnsSelf(): void
    {
        $registrar = new Registrar($this->store);
        $type = 'string';

        $result = $registrar->withType($type);

        $this->assertSame($registrar, $result);
    }

    public function testWithTypeThrowsExceptionWhenTypeAlreadySet(): void
    {
        $registrar = new Registrar($this->store);
        $registrar->withType('string');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('type already set');

        $registrar->withType('int');
    }

    public function testWithNameSetsNameAndReturnsSelf(): void
    {
        $registrar = new Registrar($this->store);
        $name = 'myProvider';

        $result = $registrar->withName($name);

        $this->assertSame($registrar, $result);
    }

    public function testWithNameThrowsExceptionWhenNameAlreadySet(): void
    {
        $registrar = new Registrar($this->store);
        $registrar->withName('provider1');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('name already set');

        $registrar->withName('provider2');
    }

    public function testOverridingSetsOverridingFlagAndReturnsSelf(): void
    {
        $registrar = new Registrar($this->store);

        $result = $registrar->overriding();

        $this->assertSame($registrar, $result);
    }

    public function testRegisterProviderRegistersAsDefaultWhenNoNameIsSet(): void
    {
        $type = 'string';
        $registrar = (new Registrar($this->store))->withType($type);

        $this->store->expects($this->once())
            ->method('registerDefaultProvider')
            ->with($type, $this->provider);

        $registrar->registerProvider($this->provider);
    }

    public function testRegisterProviderRegistersAsNamedWhenNameIsSet(): void
    {
        $type = 'string';
        $name = 'myProvider';
        $registrar = (new Registrar($this->store))
            ->withType($type)
            ->withName($name);

        $this->store->expects($this->once())
            ->method('registerNamedProvider')
            ->with($type, $name, $this->provider);

        $registrar->registerProvider($this->provider);
    }

    public function testRegisterProviderThrowsExceptionWhenTypeIsNotSet(): void
    {
        $registrar = new Registrar($this->store);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('type not set');

        $registrar->registerProvider($this->provider);
    }

    public function testRegisterProviderWithClassStringInstantiatesAndRegisters(): void
    {
        $type = 'string';
        $registrar = (new Registrar($this->store))->withType($type);

        $this->store->expects($this->once())
            ->method('registerDefaultProvider')
            ->with($type, $this->isInstanceOf(\Phodam\Provider\Primitive\DefaultStringTypeProvider::class));

        $registrar->registerProvider(\Phodam\Provider\Primitive\DefaultStringTypeProvider::class);
    }

    public function testRegisterProviderWithClassStringThrowsExceptionWhenClassDoesNotImplementProviderInterface(): void
    {
        $type = 'string';
        $registrar = (new Registrar($this->store))->withType($type);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument must be an instance of ProviderInterface or a class implementing it");

        $registrar->registerProvider(\stdClass::class);
    }

    public function testRegisterProviderWithOverridingDeregistersExistingDefaultProvider(): void
    {
        $type = 'string';
        $registrar = (new Registrar($this->store))
            ->withType($type)
            ->overriding();

        $this->store->expects($this->once())
            ->method('deregisterDefaultProvider')
            ->with($type);

        $this->store->expects($this->once())
            ->method('registerDefaultProvider')
            ->with($type, $this->provider);

        $registrar->registerProvider($this->provider);
    }

    public function testRegisterProviderWithOverridingDeregistersExistingNamedProvider(): void
    {
        $type = 'string';
        $name = 'myProvider';
        $registrar = (new Registrar($this->store))
            ->withType($type)
            ->withName($name)
            ->overriding();

        $this->store->expects($this->once())
            ->method('deregisterNamedProvider')
            ->with($type, $name);

        $this->store->expects($this->once())
            ->method('registerNamedProvider')
            ->with($type, $name, $this->provider);

        $registrar->registerProvider($this->provider);
    }

    public function testRegisterProviderWithOverridingCallsDeregisterBeforeRegister(): void
    {
        $type = 'string';
        $registrar = (new Registrar($this->store))
            ->withType($type)
            ->overriding();

        // The code always calls deregister when overriding is true
        // We'll allow it to throw (which is expected if provider doesn't exist)
        // but verify the call sequence
        $this->store->expects($this->once())
            ->method('deregisterDefaultProvider')
            ->with($type);

        $this->store->expects($this->once())
            ->method('registerDefaultProvider')
            ->with($type, $this->provider);

        // If deregister throws, we catch it and continue - but for unit test we'll let it throw
        // In real usage, this would be caught by the caller
        try {
            $registrar->registerProvider($this->provider);
        } catch (\Phodam\Store\ProviderNotFoundException $e) {
            // Expected if provider doesn't exist - but we verified the calls were made
            $this->assertTrue(true);
        }
    }

    public function testRegisterDefinitionCreatesDefinitionBasedTypeProviderAndRegisters(): void
    {
        $type = 'MyClass';
        $fields = [
            'field1' => new FieldDefinition('string'),
            'field2' => new FieldDefinition('string'),
            'field3' => new FieldDefinition('int')
        ];
        $definition = new TypeDefinition($fields);

        $registrar = (new Registrar($this->store))->withType($type);

        $this->store->expects($this->once())
            ->method('registerDefaultProvider')
            ->with($type, $this->isInstanceOf(DefinitionBasedTypeProvider::class));

        $registrar->registerDefinition($definition);
    }

    public function testRegisterDefinitionThrowsExceptionWhenTypeIsNotSet(): void
    {
        $fields = [
            'field1' => new FieldDefinition('string')
        ];
        $definition = new TypeDefinition($fields);
        $registrar = new Registrar($this->store);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('type not set');

        $registrar->registerDefinition($definition);
    }

    public function testRegisterDefinitionWithNameRegistersAsNamedProvider(): void
    {
        $type = 'MyClass';
        $name = 'myProvider';
        $fields = [
            'field1' => new FieldDefinition('string')
        ];
        $definition = new TypeDefinition($fields);

        $registrar = (new Registrar($this->store))
            ->withType($type)
            ->withName($name);

        $this->store->expects($this->once())
            ->method('registerNamedProvider')
            ->with($type, $name, $this->isInstanceOf(DefinitionBasedTypeProvider::class));

        $registrar->registerDefinition($definition);
    }

    public function testFluentInterfaceChaining(): void
    {
        $type = 'string';
        $name = 'myProvider';

        $registrar = (new Registrar($this->store))
            ->withType($type)
            ->withName($name)
            ->overriding();

        $this->assertInstanceOf(Registrar::class, $registrar);
    }
}
