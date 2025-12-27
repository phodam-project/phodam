<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex04_DefinitionBasedProviders;

class OrderItem
{
    private int $itemId;
    private string $productName;
    private float $quantity;
    private float $unitPrice;

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function setItemId(int $itemId): OrderItem
    {
        $this->itemId = $itemId;
        return $this;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): OrderItem
    {
        $this->productName = $productName;
        return $this;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): OrderItem
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
}

