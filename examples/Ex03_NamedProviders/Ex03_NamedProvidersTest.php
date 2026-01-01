<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex03_NamedProviders;

use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class Ex03_NamedProvidersTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();

        // Register named providers for User class
        // Multiple providers for the same type, each with a unique name
        $schema->forType(User::class)
            ->withName('active')
            ->registerProvider(new ActiveUserProvider());

        $schema->forType(User::class)
            ->withName('inactive')
            ->registerProvider(new InactiveUserProvider());

        // Register a named array provider
        // Array providers MUST be named (you cannot register a default array provider)
        $schema->forType('array')
            ->withName('userProfile')
            ->registerProvider(new UserProfileArrayProvider());

        $this->phodam = $schema->getPhodam();
    }

    public function testUsingNamedActiveUserProvider(): void
    {
        // Use the 'active' named provider
        $activeUser = $this->phodam->create(User::class, 'active');
        // var_export($activeUser);

        $this->assertInstanceOf(User::class, $activeUser);
        $this->assertIsString($activeUser->getName());
        $this->assertIsString($activeUser->getEmail());
        $this->assertTrue($activeUser->isActive()); // Always true for 'active' provider

        // Test multiple instances to ensure consistency
        for ($i = 0; $i < 10; $i++) {
            $user = $this->phodam->create(User::class, 'active');
            $this->assertTrue($user->isActive());
        }
    }

    public function testUsingNamedInactiveUserProvider(): void
    {
        // Use the 'inactive' named provider
        $inactiveUser = $this->phodam->create(User::class, 'inactive');
        // var_export($inactiveUser);

        $this->assertInstanceOf(User::class, $inactiveUser);
        $this->assertIsString($inactiveUser->getName());
        $this->assertIsString($inactiveUser->getEmail());
        $this->assertFalse($inactiveUser->isActive()); // Always false for 'inactive' provider

        // Test multiple instances to ensure consistency
        for ($i = 0; $i < 10; $i++) {
            $user = $this->phodam->create(User::class, 'inactive');
            $this->assertFalse($user->isActive());
        }
    }

    public function testUsingNamedProviderWithOverrides(): void
    {
        // Named providers work seamlessly with overrides
        $activeUser = $this->phodam->create(
            User::class,
            'active',
            ['name' => 'John Doe', 'email' => 'john@example.com']
        );

        $this->assertEquals('John Doe', $activeUser->getName());
        $this->assertEquals('john@example.com', $activeUser->getEmail());
        $this->assertTrue($activeUser->isActive()); // Still active (override didn't change this)

        // Even with overrides, inactive provider still creates inactive users
        $inactiveUser = $this->phodam->create(
            User::class,
            'inactive',
            ['name' => 'Jane Doe']
        );

        $this->assertEquals('Jane Doe', $inactiveUser->getName());
        $this->assertFalse($inactiveUser->isActive());
    }

    public function testUsingNamedArrayProvider(): void
    {
        // Use the named array provider
        $profile = $this->phodam->createArray('userProfile');
        // var_export($profile);

        $this->assertIsArray($profile);
        $this->assertArrayHasKey('firstName', $profile);
        $this->assertArrayHasKey('lastName', $profile);
        $this->assertArrayHasKey('email', $profile);
        $this->assertArrayHasKey('age', $profile);

        $this->assertIsString($profile['firstName']);
        $this->assertIsString($profile['lastName']);
        $this->assertIsString($profile['email']);
        $this->assertIsInt($profile['age']);
        $this->assertGreaterThanOrEqual(18, $profile['age']);
        $this->assertLessThanOrEqual(100, $profile['age']);
    }

    public function testUsingNamedArrayProviderWithOverrides(): void
    {
        // Array providers work with overrides
        $profile = $this->phodam->createArray(
            'userProfile',
            ['email' => 'custom@example.com', 'age' => 25]
        );

        $this->assertIsArray($profile);
        $this->assertArrayHasKey('firstName', $profile);
        $this->assertArrayHasKey('lastName', $profile);
        $this->assertEquals('custom@example.com', $profile['email']);
        $this->assertEquals(25, $profile['age']);
    }

    public function testMultipleNamedProvidersForSameType(): void
    {
        // Demonstrate that we can have multiple named providers for the same type
        $activeUser = $this->phodam->create(User::class, 'active');
        $inactiveUser = $this->phodam->create(User::class, 'inactive');

        $this->assertInstanceOf(User::class, $activeUser);
        $this->assertInstanceOf(User::class, $inactiveUser);
        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
    }
}

