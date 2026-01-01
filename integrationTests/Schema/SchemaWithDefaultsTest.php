<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\Schema;

use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\PhodamSchema::class)]
class SchemaWithDefaultsTest extends IntegrationBaseTestCase
{
    public function testWithDefaultsCreatesFullyFunctionalPhodam(): void
    {
        $schema = PhodamSchema::withDefaults();
        $phodam = $schema->getPhodam();

        $this->assertInstanceOf(PhodamInterface::class, $phodam);
    }

    public function testWithDefaultsRegistersAllPrimitiveProviders(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Test all primitive types
        $intValue = $phodam->create('int');
        $this->assertIsInt($intValue);

        $floatValue = $phodam->create('float');
        $this->assertIsFloat($floatValue);

        $stringValue = $phodam->create('string');
        $this->assertIsString($stringValue);

        $boolValue = $phodam->create('bool');
        $this->assertIsBool($boolValue);
    }

    public function testWithDefaultsRegistersAllBuiltinProviders(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Test all builtin types
        $dateTime = $phodam->create(\DateTime::class);
        $this->assertInstanceOf(\DateTime::class, $dateTime);

        $dateTimeImmutable = $phodam->create(\DateTimeImmutable::class);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTimeImmutable);

        $dateInterval = $phodam->create(\DateInterval::class);
        $this->assertInstanceOf(\DateInterval::class, $dateInterval);

        $datePeriod = $phodam->create(\DatePeriod::class);
        $this->assertInstanceOf(\DatePeriod::class, $datePeriod);

        $dateTimeZone = $phodam->create(\DateTimeZone::class);
        $this->assertInstanceOf(\DateTimeZone::class, $dateTimeZone);
    }

    public function testBlankSchemaCreatesEmptyPhodam(): void
    {
        $schema = PhodamSchema::blank();
        $phodam = $schema->getPhodam();

        $this->assertInstanceOf(\Phodam\PhodamInterface::class, $phodam);

        // Should throw exception for unregistered class types
        $this->expectException(\Phodam\Store\ProviderNotFoundException::class);
        $this->expectExceptionMessage('No default provider found');

        /** @var \Phodam\Phodam $phodam */
        $phodam->getTypeProvider(\PhodamTests\Fixtures\UnregisteredClassType::class);
    }

    public function testBlankSchemaCanRegisterCustomProviders(): void
    {
        $schema = PhodamSchema::blank();
        $provider = new \Phodam\Provider\Primitive\DefaultStringTypeProvider();

        $schema->forType('string')
            ->registerProvider($provider);

        $phodam = $schema->getPhodam();
        $stringValue = $phodam->create('string');

        $this->assertIsString($stringValue);
    }
}

