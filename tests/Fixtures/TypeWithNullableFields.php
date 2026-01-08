<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Fixtures;

class TypeWithNullableFields
{
    private int $id;
    private ?string $optionalString;
    private ?ChildType $optionalChild;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getOptionalString(): ?string
    {
        return $this->optionalString;
    }

    public function setOptionalString(?string $optionalString): self
    {
        $this->optionalString = $optionalString;
        return $this;
    }

    public function getOptionalChild(): ?ChildType
    {
        return $this->optionalChild;
    }

    public function setOptionalChild(?ChildType $optionalChild): self
    {
        $this->optionalChild = $optionalChild;
        return $this;
    }
}

