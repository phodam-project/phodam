<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace PhodamExamples\Ex07_BreakingCircularReferences;

class Order
{
    private int $id;
    /** @var OrderItem[] */
    private array $items;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Order
    {
        $this->id = $id;
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): Order
    {
        $this->items = $items;
        return $this;
    }
}
