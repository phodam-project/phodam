<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamExamples\Ex06_EnumProvider;

/**
 * Example class with multiple enum fields
 */
class Project
{
    private int $id;
    private string $name;
    private Priority $priority;
    private OrderStatus $status;
    private UserRole $assignedRole;
    private ?string $description;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Project
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Project
    {
        $this->name = $name;
        return $this;
    }

    public function getPriority(): Priority
    {
        return $this->priority;
    }

    public function setPriority(Priority $priority): Project
    {
        $this->priority = $priority;
        return $this;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): Project
    {
        $this->status = $status;
        return $this;
    }

    public function getAssignedRole(): UserRole
    {
        return $this->assignedRole;
    }

    public function setAssignedRole(UserRole $assignedRole): Project
    {
        $this->assignedRole = $assignedRole;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Project
    {
        $this->description = $description;
        return $this;
    }
}
