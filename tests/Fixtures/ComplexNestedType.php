<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Fixtures;

class ComplexNestedType
{
    private int $id;
    private ParentType $parent;
    private ChildType $child1;
    private ChildType $child2;
    private ?GrandchildType $optionalGrandchild;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getParent(): ParentType
    {
        return $this->parent;
    }

    public function setParent(ParentType $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function getChild1(): ChildType
    {
        return $this->child1;
    }

    public function setChild1(ChildType $child1): self
    {
        $this->child1 = $child1;
        return $this;
    }

    public function getChild2(): ChildType
    {
        return $this->child2;
    }

    public function setChild2(ChildType $child2): self
    {
        $this->child2 = $child2;
        return $this;
    }

    public function getOptionalGrandchild(): ?GrandchildType
    {
        return $this->optionalGrandchild;
    }

    public function setOptionalGrandchild(?GrandchildType $optionalGrandchild): self
    {
        $this->optionalGrandchild = $optionalGrandchild;
        return $this;
    }
}

