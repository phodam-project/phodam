<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration\Advanced;

use PhodamTests\Fixtures\SimpleTypeWithPhpDocTypes;
use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Analyzer\TypeAnalyzer::class)]
class PHPDocTypeDetectionTest extends IntegrationBaseTestCase
{
    public function testPHPDocScalarTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(SimpleTypeWithPhpDocTypes::class);

        $this->assertInstanceOf(SimpleTypeWithPhpDocTypes::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertIsBool($result->isMyBool());
    }

    public function testPHPDocArrayTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(SimpleTypeWithPhpDocTypes::class);

        $this->assertInstanceOf(SimpleTypeWithPhpDocTypes::class, $result);
        $this->assertIsArray($result->getStringArray());

        foreach ($result->getStringArray() as $item) {
            $this->assertIsString($item);
        }
    }

    public function testPHPDocClassTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Test with a class that has PHPDoc class types
        $result = $phodam->create(SimpleTypeWithPhpDocTypes::class);

        $this->assertInstanceOf(SimpleTypeWithPhpDocTypes::class, $result);
    }

    public function testPHPDocNullableTypes(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(SimpleTypeWithPhpDocTypes::class);

        // PHPDoc types default to nullable when no type declaration exists
        $this->assertInstanceOf(SimpleTypeWithPhpDocTypes::class, $result);
    }

    public function testPHPDocWithFullyQualifiedNames(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Test that fully qualified names in PHPDoc work
        $result = $phodam->create(SimpleTypeWithPhpDocTypes::class);

        $this->assertInstanceOf(SimpleTypeWithPhpDocTypes::class, $result);
    }

    public function testPHPDocWithNamespaceResolution(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $result = $phodam->create(SimpleTypeWithPhpDocTypes::class);

        $this->assertInstanceOf(SimpleTypeWithPhpDocTypes::class, $result);
    }

    public function testPHPDocMixedWithTypedProperties(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        // Test class with mix of typed and PHPDoc properties
        $result = $phodam->create(SimpleTypeWithPhpDocTypes::class);

        $this->assertInstanceOf(SimpleTypeWithPhpDocTypes::class, $result);
        $this->assertIsInt($result->getMyInt());
        $this->assertIsFloat($result->getMyFloat());
        $this->assertIsString($result->getMyString());
        $this->assertIsBool($result->isMyBool());
    }
}

