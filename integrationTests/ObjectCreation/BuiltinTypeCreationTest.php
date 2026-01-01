<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ObjectCreation;

use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Phodam::class)]
class BuiltinTypeCreationTest extends IntegrationBaseTestCase
{
    public function testCreateDateTime(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $dateTime = $phodam->create(\DateTime::class);

        $this->assertInstanceOf(\DateTime::class, $dateTime);
    }

    public function testCreateDateTimeImmutable(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $dateTimeImmutable = $phodam->create(\DateTimeImmutable::class);

        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTimeImmutable);
    }

    public function testCreateDateInterval(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $dateInterval = $phodam->create(\DateInterval::class);

        $this->assertInstanceOf(\DateInterval::class, $dateInterval);
    }

    public function testCreateDatePeriod(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $datePeriod = $phodam->create(\DatePeriod::class);

        $this->assertInstanceOf(\DatePeriod::class, $datePeriod);
    }

    public function testCreateDateTimeZone(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $dateTimeZone = $phodam->create(\DateTimeZone::class);

        $this->assertInstanceOf(\DateTimeZone::class, $dateTimeZone);
    }

    public function testBuiltinTypesAreValidInstances(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $dateTime = $phodam->create(\DateTime::class);
        $dateTimeImmutable = $phodam->create(\DateTimeImmutable::class);
        $dateInterval = $phodam->create(\DateInterval::class);
        $datePeriod = $phodam->create(\DatePeriod::class);
        $dateTimeZone = $phodam->create(\DateTimeZone::class);

        // Verify they are valid instances that can be used
        $this->assertInstanceOf(\DateTime::class, $dateTime);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dateTimeImmutable);
        $this->assertInstanceOf(\DateInterval::class, $dateInterval);
        $this->assertInstanceOf(\DatePeriod::class, $datePeriod);
        $this->assertInstanceOf(\DateTimeZone::class, $dateTimeZone);

        // Verify they have expected methods
        $this->assertIsString($dateTime->format('Y-m-d'));
        $this->assertIsString($dateTimeImmutable->format('Y-m-d'));
        $this->assertIsString($dateTimeZone->getName());
    }
}

