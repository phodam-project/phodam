<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamExamples\Ex04_DefinitionBasedProviders;

/**
 * Example class with array field that needs element type definition
 */
class Order
{
    private int $orderId;
    private array $items;  // Array without element type - needs definition

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): Order
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Order
     */
    public function setItems(array $items): Order
    {
        $this->items = $items;
        return $this;
    }
}

