<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex02_CustomTypeProviders;

class Classroom
{
    private int $roomNumber;

    /** @var array<Student> */
    private array $students;

    /**
     * @return int
     */
    public function getRoomNumber(): int
    {
        return $this->roomNumber;
    }

    /**
     * @param int $roomNumber
     * @return Classroom
     */
    public function setRoomNumber(int $roomNumber): Classroom
    {
        $this->roomNumber = $roomNumber;
        return $this;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array
    {
        return $this->students;
    }

    /**
     * @param Student[] $students
     * @return Classroom
     */
    public function setStudents(array $students): Classroom
    {
        $this->students = $students;
        return $this;
    }
}