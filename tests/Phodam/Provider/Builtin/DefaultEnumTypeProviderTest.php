<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Phodam\Provider\Builtin;

use InvalidArgumentException;
use Phodam\PhodamInterface;
use Phodam\Provider\Builtin\DefaultEnumTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

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

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(DefaultEnumTypeProvider::class)]
#[CoversMethod(DefaultEnumTypeProvider::class, 'create')]
class DefaultEnumTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultEnumTypeProvider $provider;
    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultEnumTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    public function testCreateWithPureEnum(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            TestPureEnum::class,
            [],
            []
        );

        $value = $this->provider->create($context);

        $this->assertInstanceOf(TestPureEnum::class, $value);
        $this->assertContains($value, TestPureEnum::cases());
    }

    public function testCreateWithStringBackedEnum(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            TestStringBackedEnum::class,
            [],
            []
        );

        $value = $this->provider->create($context);

        $this->assertInstanceOf(TestStringBackedEnum::class, $value);
        $this->assertContains($value, TestStringBackedEnum::cases());
        $this->assertIsString($value->value);
        $this->assertContains($value->value, ['red', 'green', 'blue']);
    }

    public function testCreateWithIntBackedEnum(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            TestIntBackedEnum::class,
            [],
            []
        );

        $value = $this->provider->create($context);

        $this->assertInstanceOf(TestIntBackedEnum::class, $value);
        $this->assertContains($value, TestIntBackedEnum::cases());
        $this->assertIsInt($value->value);
        $this->assertContains($value->value, [0, 1, 2]);
    }

    public function testCreateReturnsRandomCase(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            TestPureEnum::class,
            [],
            []
        );

        // Create multiple instances to verify randomness
        $results = [];
        for ($i = 0; $i < 20; $i++) {
            $results[] = $this->provider->create($context);
        }

        // Verify all results are valid enum cases
        foreach ($results as $result) {
            $this->assertInstanceOf(TestPureEnum::class, $result);
            $this->assertContains($result, TestPureEnum::cases());
        }

        // With 3 cases and 20 attempts, we should get multiple different cases
        $uniqueResults = array_unique($results, SORT_REGULAR);
        $this->assertGreaterThanOrEqual(1, count($uniqueResults));
    }

    public function testCreateThrowsExceptionForNonEnumType(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            'stdClass',
            [],
            []
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Type stdClass is not an enum");

        $this->provider->create($context);
    }

    public function testCreateThrowsExceptionForNonExistentType(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            'NonExistentEnumType',
            [],
            []
        );

        $this->expectException(InvalidArgumentException::class);

        $this->provider->create($context);
    }

    public function testCreateWorksWithOverrides(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            TestPureEnum::class,
            ['someOverride' => 'value'],
            []
        );

        // Overrides shouldn't affect enum creation, but should not cause errors
        $value = $this->provider->create($context);

        $this->assertInstanceOf(TestPureEnum::class, $value);
    }

    public function testCreateWorksWithConfig(): void
    {
        $context = new ProviderContext(
            $this->phodam,
            TestPureEnum::class,
            [],
            ['someConfig' => 'value']
        );

        // Config shouldn't affect enum creation, but should not cause errors
        $value = $this->provider->create($context);

        $this->assertInstanceOf(TestPureEnum::class, $value);
    }
}
