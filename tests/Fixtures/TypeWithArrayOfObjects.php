<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

/**
 * @var ChildType[]
 */
class TypeWithArrayOfObjects
{
    private int $id;
    /**
     * @var ChildType[]
     */
    private array $children;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return ChildType[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param ChildType[] $children
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;
        return $this;
    }
}

