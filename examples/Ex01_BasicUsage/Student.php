<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex01_BasicUsage;

class Student
{
    private int $id;
    private string $name;
    private float $gpa;
    private bool $active;
    private Address $address;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Student
     */
    public function setId(int $id): Student
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Student
     */
    public function setName(string $name): Student
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float
     */
    public function getGpa(): float
    {
        return $this->gpa;
    }

    /**
     * @param float $gpa
     * @return Student
     */
    public function setGpa(float $gpa): Student
    {
        $this->gpa = $gpa;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Student
     */
    public function setActive(bool $active): Student
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return Student
     */
    public function setAddress(Address $address): Student
    {
        $this->address = $address;
        return $this;
    }
}
