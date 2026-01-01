<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\ObjectCreation;

use PhodamTests\Fixtures\ChildType;
use PhodamTests\Fixtures\ComplexNestedType;
use PhodamTests\Fixtures\ParentType;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Phodam::class)]
class NestedObjectCreationTest extends IntegrationBaseTestCase
{
    public function testCreateObjectWithNestedObject(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(ParentType::class);

        $this->assertInstanceOf(ParentType::class, $result);
        $this->assertIsInt($result->getId());
        $this->assertIsString($result->getName());
        $this->assertInstanceOf(ChildType::class, $result->getChild());
    }

    public function testCreateObjectWithMultipleNestedObjects(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(ComplexNestedType::class);

        $this->assertInstanceOf(ComplexNestedType::class, $result);
        $this->assertInstanceOf(ParentType::class, $result->getParent());
        $this->assertInstanceOf(ChildType::class, $result->getChild1());
        $this->assertInstanceOf(ChildType::class, $result->getChild2());
    }

    public function testCreateDeeplyNestedObjects(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(ParentType::class);

        // Parent -> Child (2 levels)
        $this->assertInstanceOf(ParentType::class, $result);
        $child = $result->getChild();
        $this->assertInstanceOf(ChildType::class, $child);
    }

    public function testNestedObjectsAreFullyPopulated(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(ParentType::class);

        $this->assertInstanceOf(ParentType::class, $result);
        $this->assertIsInt($result->getId());
        $this->assertIsString($result->getName());

        $child = $result->getChild();
        $this->assertInstanceOf(ChildType::class, $child);
        $this->assertIsInt($child->getValue());
        $this->assertIsString($child->getDescription());
    }

    public function testNestedObjectsUseCorrectTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(ParentType::class);

        $this->assertInstanceOf(ParentType::class, $result);
        $this->assertInstanceOf(ChildType::class, $result->getChild());
    }

    public function testNestedObjectsCanHaveOverrides(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $overrides = [
            'id' => 999,
            'name' => 'Custom Name',
        ];

        $result = $phodam->create(ParentType::class, null, $overrides);

        $this->assertInstanceOf(ParentType::class, $result);
        $this->assertEquals(999, $result->getId());
        $this->assertEquals('Custom Name', $result->getName());
        // Child should still be auto-generated
        $this->assertInstanceOf(ChildType::class, $result->getChild());
    }
}

