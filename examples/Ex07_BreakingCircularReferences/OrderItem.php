<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace PhodamExamples\Ex07_BreakingCircularReferences;

class OrderItem
{
    private int $id;
    private string $name;
    private int $quantity;
    private float $unitPrice;
    private Order $order;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): OrderItem
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): OrderItem
    {
        $this->name = $name;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): OrderItem
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): OrderItem
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): OrderItem
    {
        $this->order = $order;
        return $this;
    }
}
