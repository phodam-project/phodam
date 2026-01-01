<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Phodam\PhodamSchemaInterface;
use Phodam\Provider\DefaultProviderBundle;
use Phodam\Store\RegistrarInterface;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(\Phodam\Provider\DefaultProviderBundle::class)]
#[CoversMethod(\Phodam\Provider\DefaultProviderBundle::class, 'register')]
class DefaultProviderBundleTest extends PhodamBaseTestCase
{
    /** @var PhodamSchemaInterface&MockObject */
    private $schema;

    public function setUp(): void
    {
        parent::setUp();
        $this->schema = $this->createMock(PhodamSchemaInterface::class);
    }

    public function testRegisterCallsForTypeForAllPrimitiveTypes(): void
    {
        $bundle = new DefaultProviderBundle();
        $registrar = $this->createMock(RegistrarInterface::class);

        $primitiveTypes = [];
        $primitiveProviders = [];

        $this->schema->method('forType')
            ->willReturnCallback(function ($type) use ($registrar, &$primitiveTypes) {
                if (in_array($type, ['bool', 'float', 'int', 'string'], true)) {
                    $primitiveTypes[] = $type;
                }
                return $registrar;
            });

        $registrar->method('registerProvider')
            ->willReturnCallback(function ($providerClass) use (&$primitiveProviders) {
                $primitiveProviderClasses = [
                    \Phodam\Provider\Primitive\DefaultBoolTypeProvider::class,
                    \Phodam\Provider\Primitive\DefaultFloatTypeProvider::class,
                    \Phodam\Provider\Primitive\DefaultIntTypeProvider::class,
                    \Phodam\Provider\Primitive\DefaultStringTypeProvider::class,
                ];
                if (in_array($providerClass, $primitiveProviderClasses, true)) {
                    $primitiveProviders[] = $providerClass;
                }
            });

        $bundle->register($this->schema);

        $this->assertCount(4, $primitiveTypes, 'Should register 4 primitive types');
        $this->assertCount(4, $primitiveProviders, 'Should register 4 primitive providers');
        $this->assertContains('bool', $primitiveTypes);
        $this->assertContains('float', $primitiveTypes);
        $this->assertContains('int', $primitiveTypes);
        $this->assertContains('string', $primitiveTypes);
    }

    public function testRegisterCallsForTypeForAllBuiltinTypes(): void
    {
        $bundle = new DefaultProviderBundle();
        $registrar = $this->createMock(RegistrarInterface::class);

        $builtinTypes = [];
        $builtinProviders = [];

        $this->schema->method('forType')
            ->willReturnCallback(function ($type) use ($registrar, &$builtinTypes) {
                $builtinTypeClasses = [
                    DateInterval::class,
                    DatePeriod::class,
                    DateTime::class,
                    DateTimeImmutable::class,
                    DateTimeZone::class,
                ];
                if (in_array($type, $builtinTypeClasses, true)) {
                    $builtinTypes[] = $type;
                }
                return $registrar;
            });

        $registrar->method('registerProvider')
            ->willReturnCallback(function ($providerClass) use (&$builtinProviders) {
                $builtinProviderClasses = [
                    \Phodam\Provider\Builtin\DefaultDateIntervalTypeProvider::class,
                    \Phodam\Provider\Builtin\DefaultDatePeriodTypeProvider::class,
                    \Phodam\Provider\Builtin\DefaultDateTimeTypeProvider::class,
                    \Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider::class,
                    \Phodam\Provider\Builtin\DefaultDateTimeZoneTypeProvider::class,
                ];
                if (in_array($providerClass, $builtinProviderClasses, true)) {
                    $builtinProviders[] = $providerClass;
                }
            });

        $bundle->register($this->schema);

        $this->assertCount(5, $builtinTypes, 'Should register 5 builtin types');
        $this->assertCount(5, $builtinProviders, 'Should register 5 builtin providers');
        $this->assertContains(DateInterval::class, $builtinTypes);
        $this->assertContains(DatePeriod::class, $builtinTypes);
        $this->assertContains(DateTime::class, $builtinTypes);
        $this->assertContains(DateTimeImmutable::class, $builtinTypes);
        $this->assertContains(DateTimeZone::class, $builtinTypes);
    }

    public function testRegisterCallsForTypeForAllTypesInCorrectOrder(): void
    {
        $bundle = new DefaultProviderBundle();
        $registrar = $this->createMock(RegistrarInterface::class);

        // Expect 9 total calls (4 primitives + 5 builtin)
        $this->schema->expects($this->exactly(9))
            ->method('forType')
            ->willReturn($registrar);

        $registrar->expects($this->exactly(9))
            ->method('registerProvider');

        $bundle->register($this->schema);
    }

    public function testRegisterCallsRegisterProviderWithCorrectProviderClasses(): void
    {
        $bundle = new DefaultProviderBundle();
        $registrar = $this->createMock(RegistrarInterface::class);

        $this->schema->method('forType')
            ->willReturn($registrar);

        $registrar->expects($this->exactly(9))
            ->method('registerProvider')
            ->with($this->logicalOr(
                \Phodam\Provider\Primitive\DefaultBoolTypeProvider::class,
                \Phodam\Provider\Primitive\DefaultFloatTypeProvider::class,
                \Phodam\Provider\Primitive\DefaultIntTypeProvider::class,
                \Phodam\Provider\Primitive\DefaultStringTypeProvider::class,
                \Phodam\Provider\Builtin\DefaultDateIntervalTypeProvider::class,
                \Phodam\Provider\Builtin\DefaultDatePeriodTypeProvider::class,
                \Phodam\Provider\Builtin\DefaultDateTimeTypeProvider::class,
                \Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider::class,
                \Phodam\Provider\Builtin\DefaultDateTimeZoneTypeProvider::class
            ));

        $bundle->register($this->schema);
    }
}
