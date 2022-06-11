<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex02_CustomTypeProviders;

use DateTimeImmutable;
use Phodam\Analyzer\FieldDefinition;
use Phodam\Analyzer\TypeAnalysisException;
use Phodam\Phodam;
use Phodam\Provider\ProviderConfig;
use PHPUnit\Framework\TestCase;

class Ex02_CustomTypeProvidersTest extends TestCase
{
    private Phodam $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $this->phodam = new Phodam();

        // Tired of specifying active = true, 0.0 <= gpa <= 4.0 ?
        // Make a provider to have your own defaults!
        $studentProvider = new StudentTypeProvider();
        $studentProviderConfig = (new ProviderConfig($studentProvider))
            ->forType(Student::class);

        $this->phodam->registerProviderConfig($studentProviderConfig);

        // A Classroom provider is needed because `array $students`
        // doesn't have a type, and we can't assume because of 'array'
        $classroomProvider = new ClassroomTypeProvider();
        $classroomProviderConfig = (new ProviderConfig($classroomProvider))
            ->forType(Classroom::class);

        $this->phodam->registerProviderConfig($classroomProviderConfig);
    }

    public function testBeforeRegistering(): void
    {
        // we expect a new instance of Phodam to not understand how to
        // make a Classroom because it's unable to map `array $students` to
        // a type it can generate
        $localPhodam = new Phodam();

        $this->expectException(TypeAnalysisException::class);
        $this->expectExceptionMessage('PhodamExamples\Ex02_CustomTypeProviders\Classroom: Unable to map fields: students');

        $localPhodam->create(Classroom::class);
    }

    public function testCreatingClassroom(): void
    {
        $classroom = $this->phodam->create(Classroom::class);
        // var_export($classroom);
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

    public function testTypeDefinitionWithArray(): void
    {
        $def = [
            'students' => (new FieldDefinition(Student::class))
                ->setArray(true)
        ];

        $localPhodam = new Phodam();
        $localPhodam->registerTypeDefinition(Classroom::class, $def);

        /** @var Classroom $classroom */
        $classroom = $localPhodam->create(Classroom::class);
        // var_export($classroom);
        $this->assertInstanceOf(Classroom::class, $classroom);
        $this->assertIsInt($classroom->getRoomNumber());
        $this->assertIsArray($classroom->getStudents());
        foreach ($classroom->getStudents() as $student) {
            $this->assertInstanceOf(Student::class, $student);
            $this->assertInstanceOf(Address::class, $student->getAddress());
            $this->assertInstanceOf(DateTimeImmutable::class, $student->getDateOfBirth());
        }
    }
}
