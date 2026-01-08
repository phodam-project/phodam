<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex02_CustomTypeProviders;

use DateTimeImmutable;
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class Ex02_CustomTypeProvidersTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();

        // Tired of specifying active = true, 0.0 <= gpa <= 4.0 ?
        // Make a provider to have your own defaults!
        // The providers are registered using the PhodamProvider attribute
        $schema->registerProvider(StudentTypeProvider::class);
        $schema->registerProvider(ClassroomTypeProvider::class);

        $this->phodam = $schema->getPhodam();
    }

    public function testCreatingClassroom(): void
    {
        $classroom = $this->phodam->create(Classroom::class);

        $this->assertInstanceOf(Classroom::class, $classroom);
        $this->assertIsInt($classroom->getRoomNumber());
        $this->assertIsArray($classroom->getStudents());
        foreach ($classroom->getStudents() as $student) {
            $this->assertInstanceOf(Student::class, $student);
            $this->assertGreaterThanOrEqual(0.0, $student->getGpa());
            $this->assertLessThanOrEqual(4.0, $student->getGpa());
            $this->assertTrue($student->isActive());
            $this->assertInstanceOf(Address::class, $student->getAddress());
            $this->assertInstanceOf(DateTimeImmutable::class, $student->getDateOfBirth());
        }
    }
}
