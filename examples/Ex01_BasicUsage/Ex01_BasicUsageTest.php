<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex01_BasicUsage;

use DateTimeImmutable;
use Phodam\Phodam;
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class Ex01_BasicUsageTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();
        $this->phodam = $schema->getPhodam();
    }

    public function testCreateInt(): void
    {
        $int = $this->phodam->create('int');
        $this->assertIsInt($int);

        // The default int provider can take some values in its configuration
        $positiveIntConfig = ['min' => 0, 'max' => PHP_INT_MAX];

        for ($i = 0; $i < 10; $i++) {
            $positiveInt = $this->phodam->create(
                'int',
                config: $positiveIntConfig,
            );
            $this->assertIsInt($positiveInt);
            $this->assertGreaterThanOrEqual(0, $positiveInt);
            $this->assertLessThanOrEqual(PHP_INT_MAX, $positiveInt);
        }
    }

    public function testCreateFloat(): void
    {
        $float = $this->phodam->create('float');
        $this->assertIsFloat($float);

        // The default float provider can take some values in its configuration
        $randomTestScoreConfig = [
            'min' => 0.0,
            'max' => 100.0,
            'precision' => 1
        ];

        for ($i = 0; $i < 10; $i++) {
            $randomTestScore = $this->phodam->create(
                'float',
                config: $randomTestScoreConfig,
            );
            $this->assertIsFloat($randomTestScore);
            $this->assertGreaterThanOrEqual(0.0, $randomTestScore);
            $this->assertLessThanOrEqual(100.0, $randomTestScore);
        }
    }

    public function testCreateString(): void
    {
        $string = $this->phodam->create('string');
        $this->assertIsString($string);

        // The default string provider can take some values in its configuration
        $stringConfig = [
            'minLength' => 10,
            'maxLength' => 20,
            'type' => 'upper'
        ];

        for ($i = 0; $i < 10; $i++) {
            $randomString = $this->phodam->create(
                'string',
                config: $stringConfig,
            );
            $this->assertIsString($randomString);
            $strlen = strlen($randomString);
            $this->assertGreaterThanOrEqual(10, $strlen);
            $this->assertLessThanOrEqual(20, $strlen);
            $this->assertEquals(strtoupper($randomString), $randomString);
        }
    }

    public function testCreateStudent(): void
    {
        $student = $this->phodam->create(Student::class);
        $this->assertInstanceOf(Student::class, $student);
        $this->assertIsInt($student->getId());
        $this->assertIsString($student->getName());
        $this->assertIsFloat($student->getGpa());
        $this->assertIsBool($student->isActive());
        $this->assertInstanceOf(Address::class, $student->getAddress());
    }

    public function testCreateStudentWithOverrides(): void
    {
        // every instance made will have a gpa = 4.0, and active = true
        $studentOverrides = ['gpa' => 4.0, 'active' => true];
        for ($i = 0; $i < 10; $i++) {
            $student = $this->phodam->create(
                Student::class,
                overrides: $studentOverrides
            );

            $this->assertInstanceOf(Student::class, $student);
            $this->assertIsInt($student->getId());
            $this->assertIsString($student->getName());
            $this->assertEquals(4.0, $student->getGpa());
            $this->assertTrue($student->isActive());
            $this->assertInstanceOf(Address::class, $student->getAddress());
            $this->assertInstanceOf(DateTimeImmutable::class, $student->getDateOfBirth());
        }
    }
}
