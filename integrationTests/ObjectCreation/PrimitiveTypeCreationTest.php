<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Integration\ObjectCreation;

use PhodamTests\Integration\IntegrationBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(\Phodam\Phodam::class)]
class PrimitiveTypeCreationTest extends IntegrationBaseTestCase
{
    public function testCreateInt(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $intValue = $phodam->create('int');

        $this->assertIsInt($intValue);
    }

    public function testCreateFloat(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $floatValue = $phodam->create('float');

        $this->assertIsFloat($floatValue);
    }

    public function testCreateString(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $stringValue = $phodam->create('string');

        $this->assertIsString($stringValue);
    }

    public function testCreateBool(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $boolValue = $phodam->create('bool');

        $this->assertIsBool($boolValue);
    }

    public function testPrimitiveTypesAreValid(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $intValue = $phodam->create('int');
        $floatValue = $phodam->create('float');
        $stringValue = $phodam->create('string');
        $boolValue = $phodam->create('bool');

        $this->assertIsInt($intValue);
        $this->assertIsFloat($floatValue);
        $this->assertIsString($stringValue);
        $this->assertIsBool($boolValue);
    }

    public function testPrimitiveTypesAreRandom(): void
    {
        $phodam = $this->createPhodamWithDefaults();

        $values1 = [
            'int' => $phodam->create('int'),
            'float' => $phodam->create('float'),
            'string' => $phodam->create('string'),
            'bool' => $phodam->create('bool'),
        ];

        $values2 = [
            'int' => $phodam->create('int'),
            'float' => $phodam->create('float'),
            'string' => $phodam->create('string'),
            'bool' => $phodam->create('bool'),
        ];

        // At least some values should be different (very high probability)
        $allSame = true;
        foreach ($values1 as $key => $value1) {
            if ($value1 !== $values2[$key]) {
                $allSame = false;
                break;
            }
        }

        // It's extremely unlikely all values are the same
        $this->assertFalse($allSame, 'Values should be randomized');
    }
}

