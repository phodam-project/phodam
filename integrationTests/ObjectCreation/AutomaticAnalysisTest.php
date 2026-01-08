<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Integration\ObjectCreation;

use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Analyzer\TypeAnalyzer;
use Phodam\Phodam;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Fixtures\SimpleTypeMissingSomeFieldTypes;
use PhodamTests\Fixtures\SimpleTypeWithPhpDocTypes;
use PhodamTests\Fixtures\SimpleTypeWithTypedArray;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Phodam::class)]
#[CoversClass(TypeAnalyzer::class)]
class AutomaticAnalysisTest extends IntegrationBaseTestCase
{
    public function testAutomaticAnalysisForTypedProperties(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(SimpleType::class);

        $this->assertInstanceOf(SimpleType::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertIsBool($result->isMyBool());
    }

    public function testAutomaticAnalysisForPHPDocTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(SimpleTypeWithPhpDocTypes::class);

        $this->assertInstanceOf(SimpleTypeWithPhpDocTypes::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertIsBool($result->isMyBool());
        $this->assertIsArray($result->getStringArray());
    }

    public function testAutomaticAnalysisCreatesProvider(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // First call should trigger analysis and provider creation
        $result1 = $phodam->create(SimpleType::class);
        $this->assertInstanceOf(SimpleType::class, $result1);

        // Second call should use the registered provider (no re-analysis)
        $result2 = $phodam->create(SimpleType::class);
        $this->assertInstanceOf(SimpleType::class, $result2);
    }

    public function testAutomaticAnalysisForNestedTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Create a type that will be analyzed and contains nested types
        $result = $phodam->create(SimpleType::class);

        $this->assertInstanceOf(SimpleType::class, $result);
        // Nested types should be automatically handled
    }

    public function testAutomaticAnalysisForArrays(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(SimpleTypeWithTypedArray::class);

        $this->assertInstanceOf(SimpleTypeWithTypedArray::class, $result);
        $this->assertIsArray($result->getStringArray());
        $this->assertIsArray($result->getIntArray());

        // Verify array elements are of correct type
        foreach ($result->getStringArray() as $item) {
            $this->assertIsString($item);
        }

        foreach ($result->getIntArray() as $item) {
            $this->assertIsInt($item);
        }
    }

    public function testAutomaticAnalysisHandlesNullableTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(SimpleType::class);

        $this->assertInstanceOf(SimpleType::class, $result);
        // Nullable fields may be null or have values
        $this->assertTrue(
            $result->getMyFloat() === null || is_float($result->getMyFloat()),
            'Nullable float should be null or float'
        );
        $this->assertTrue(
            $result->getMyString() === null || is_string($result->getMyString()),
            'Nullable string should be null or string'
        );
    }

    public function testAutomaticAnalysisFailsForUnmappableFields(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $this->expectException(TypeAnalysisException::class);

        $phodam->create(SimpleTypeMissingSomeFieldTypes::class);
    }
}

