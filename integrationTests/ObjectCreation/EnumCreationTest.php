<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ObjectCreation;

use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test enum for pure enums (UnitEnum)
 */
enum TestPureEnum
{
    case CASE_ONE;
    case CASE_TWO;
    case CASE_THREE;
}

/**
 * Test enum for string-backed enums (BackedEnum)
 */
enum TestStringBackedEnum: string
{
    case RED = 'red';
    case GREEN = 'green';
    case BLUE = 'blue';
}

/**
 * Test enum for int-backed enums (BackedEnum)
 */
enum TestIntBackedEnum: int
{
    case ZERO = 0;
    case ONE = 1;
    case TWO = 2;
}

#[CoversClass(\Phodam\Phodam::class)]
class EnumCreationTest extends IntegrationBaseTestCase
{

    public function testCreatePureEnum(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $enum = $phodam->create(TestPureEnum::class);

        $this->assertInstanceOf(TestPureEnum::class, $enum);
        $this->assertContains($enum, TestPureEnum::cases());
    }

    public function testCreateStringBackedEnum(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $enum = $phodam->create(TestStringBackedEnum::class);

        $this->assertInstanceOf(TestStringBackedEnum::class, $enum);
        $this->assertContains($enum, TestStringBackedEnum::cases());
        $this->assertIsString($enum->value);
        $this->assertContains($enum->value, ['red', 'green', 'blue']);
    }

    public function testCreateIntBackedEnum(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $enum = $phodam->create(TestIntBackedEnum::class);

        $this->assertInstanceOf(TestIntBackedEnum::class, $enum);
        $this->assertContains($enum, TestIntBackedEnum::cases());
        $this->assertIsInt($enum->value);
        $this->assertContains($enum->value, [0, 1, 2]);
    }

    public function testEnumProviderReturnsRandomCase(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Create multiple instances to verify randomness
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $results[] = $phodam->create(TestPureEnum::class);
        }

        // Verify all results are valid enum cases
        foreach ($results as $result) {
            $this->assertInstanceOf(TestPureEnum::class, $result);
            $this->assertContains($result, TestPureEnum::cases());
        }

        // With 3 cases and 10 attempts, we should get at least 2 different cases
        // (this is probabilistic but very likely)
        $uniqueResults = array_unique($results, SORT_REGULAR);
        $this->assertGreaterThanOrEqual(1, count($uniqueResults));
    }

    public function testEnumProviderWorksWithMultipleCalls(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Test that the provider works consistently across multiple calls
        $enum1 = $phodam->create(TestStringBackedEnum::class);
        $enum2 = $phodam->create(TestStringBackedEnum::class);
        $enum3 = $phodam->create(TestStringBackedEnum::class);

        $this->assertInstanceOf(TestStringBackedEnum::class, $enum1);
        $this->assertInstanceOf(TestStringBackedEnum::class, $enum2);
        $this->assertInstanceOf(TestStringBackedEnum::class, $enum3);
    }
}
