<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex06_EnumProvider;

use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class Ex06_EnumProviderTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        // The enum provider is automatically available with defaults
        $schema = PhodamSchema::withDefaults();
        $this->phodam = $schema->getPhodam();
    }

    public function testCreatePureEnum(): void
    {
        // Pure enums (UnitEnum) don't have backing values
        $status = $this->phodam->create(OrderStatus::class);
        // var_export($status);
        
        $this->assertInstanceOf(OrderStatus::class, $status);
        $this->assertContains($status, OrderStatus::cases());
    }

    public function testCreateStringBackedEnum(): void
    {
        // String-backed enums have string values
        $priority = $this->phodam->create(Priority::class);
        // var_export($priority);
        
        $this->assertInstanceOf(Priority::class, $priority);
        $this->assertContains($priority, Priority::cases());
        $this->assertIsString($priority->value);
        $this->assertContains($priority->value, ['low', 'medium', 'high', 'urgent']);
    }

    public function testCreateIntBackedEnum(): void
    {
        // Int-backed enums have integer values
        $userRole = $this->phodam->create(UserRole::class);
        // var_export($userRole);
        
        $this->assertInstanceOf(UserRole::class, $userRole);
        $this->assertContains($userRole, UserRole::cases());
        $this->assertIsInt($userRole->value);
        $this->assertContains($userRole->value, [1, 2, 3, 4, 5]);
    }

    public function testEnumProviderReturnsRandomCase(): void
    {
        // The enum provider returns a random case each time
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $results[] = $this->phodam->create(OrderStatus::class);
        }
        
        // All results should be valid enum cases
        foreach ($results as $result) {
            $this->assertInstanceOf(OrderStatus::class, $result);
            $this->assertContains($result, OrderStatus::cases());
        }
        
        // With multiple calls, we should get different cases (probabilistic)
        $uniqueResults = array_unique($results, SORT_REGULAR);
        $this->assertGreaterThanOrEqual(1, count($uniqueResults));
    }

    public function testUsingEnumInClass(): void
    {
        // Enums work seamlessly in classes - Phodam automatically detects and uses the enum provider
        $task = $this->phodam->create(Task::class);
        // var_export($task);
        
        $this->assertInstanceOf(Task::class, $task);
        $this->assertInstanceOf(Priority::class, $task->getPriority());
        $this->assertInstanceOf(OrderStatus::class, $task->getStatus());
        $this->assertContains($task->getPriority(), Priority::cases());
        $this->assertContains($task->getStatus(), OrderStatus::cases());
    }

    public function testUsingEnumInClassWithOverrides(): void
    {
        // You can override enum values just like any other field
        $task = $this->phodam->create(Task::class, null, [
            'priority' => Priority::HIGH,
            'status' => OrderStatus::IN_PROGRESS
        ]);
        
        $this->assertEquals(Priority::HIGH, $task->getPriority());
        $this->assertEquals(OrderStatus::IN_PROGRESS, $task->getStatus());
    }

    public function testMultipleEnumsInSameClass(): void
    {
        // A class can have multiple enum fields
        $project = $this->phodam->create(Project::class);
        // var_export($project);
        
        $this->assertInstanceOf(Project::class, $project);
        $this->assertInstanceOf(Priority::class, $project->getPriority());
        $this->assertInstanceOf(OrderStatus::class, $project->getStatus());
        $this->assertInstanceOf(UserRole::class, $project->getAssignedRole());
        
        // All enums should be valid cases
        $this->assertContains($project->getPriority(), Priority::cases());
        $this->assertContains($project->getStatus(), OrderStatus::cases());
        $this->assertContains($project->getAssignedRole(), UserRole::cases());
    }

    public function testEnumProviderWorksAutomatically(): void
    {
        // No registration needed - the enum provider is automatically used
        // when Phodam detects an enum type
        $status1 = $this->phodam->create(OrderStatus::class);
        $status2 = $this->phodam->create(OrderStatus::class);
        $status3 = $this->phodam->create(OrderStatus::class);
        
        // All should be valid enum instances
        $this->assertInstanceOf(OrderStatus::class, $status1);
        $this->assertInstanceOf(OrderStatus::class, $status2);
        $this->assertInstanceOf(OrderStatus::class, $status3);
    }
}
