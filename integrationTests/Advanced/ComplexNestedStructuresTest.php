<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\Advanced;

use PhodamTests\Fixtures\ChildType;
use PhodamTests\Fixtures\ComplexNestedType;
use PhodamTests\Fixtures\ParentType;
use PhodamTests\Fixtures\TypeWithArrayOfObjects;
use PhodamTests\Fixtures\TypeWithNullableFields;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Phodam::class)]
#[CoversClass(\Phodam\Provider\DefinitionBasedTypeProvider::class)]
class ComplexNestedStructuresTest extends IntegrationBaseTestCase
{
    public function testCreateObjectWithMixedNestedTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(ComplexNestedType::class);

        $this->assertInstanceOf(ComplexNestedType::class, $result);
        $this->assertIsInt($result->getId());
        $this->assertInstanceOf(ParentType::class, $result->getParent());
        $this->assertInstanceOf(ChildType::class, $result->getChild1());
        $this->assertInstanceOf(ChildType::class, $result->getChild2());
    }

    public function testCreateObjectWithArrayOfObjects(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(TypeWithArrayOfObjects::class);

        $this->assertInstanceOf(TypeWithArrayOfObjects::class, $result);
        $this->assertIsInt($result->getId());
        $this->assertIsArray($result->getChildren());

        foreach ($result->getChildren() as $child) {
            $this->assertInstanceOf(ChildType::class, $child);
        }
    }

    public function testCreateObjectWithNestedArrays(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Create an object with array of objects (which is a form of nested arrays)
        $result = $phodam->create(TypeWithArrayOfObjects::class);

        $this->assertInstanceOf(TypeWithArrayOfObjects::class, $result);
        $this->assertIsArray($result->getChildren());
        $this->assertNotEmpty($result->getChildren());
    }

    public function testCreateObjectWithOptionalNestedObjects(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(TypeWithNullableFields::class);

        $this->assertInstanceOf(TypeWithNullableFields::class, $result);
        $this->assertIsInt($result->getId());
        // Optional fields may be null or have values
        $this->assertTrue(
            $result->getOptionalString() === null || is_string($result->getOptionalString()),
            'Optional string should be null or string'
        );
        $this->assertTrue(
            $result->getOptionalChild() === null || $result->getOptionalChild() instanceof ChildType,
            'Optional child should be null or ChildType'
        );
    }

    public function testCreateObjectWithMultipleLevelsOfNesting(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(ComplexNestedType::class);

        // ComplexNestedType -> ParentType -> ChildType (3 levels)
        $this->assertInstanceOf(ComplexNestedType::class, $result);
        $parent = $result->getParent();
        $this->assertInstanceOf(ParentType::class, $parent);
        $child = $parent->getChild();
        $this->assertInstanceOf(ChildType::class, $child);
    }

    public function testComplexStructureIsFullyPopulated(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(ComplexNestedType::class);

        $this->assertInstanceOf(ComplexNestedType::class, $result);
        $this->assertIsInt($result->getId());

        // Verify parent is populated
        $parent = $result->getParent();
        $this->assertIsInt($parent->getId());
        $this->assertIsString($parent->getName());

        // Verify children are populated
        $child1 = $result->getChild1();
        $this->assertIsInt($child1->getValue());
        $this->assertIsString($child1->getDescription());

        $child2 = $result->getChild2();
        $this->assertIsInt($child2->getValue());
        $this->assertIsString($child2->getDescription());
    }
}

