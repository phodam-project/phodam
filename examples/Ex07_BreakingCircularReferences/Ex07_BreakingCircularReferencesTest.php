<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex07_BreakingCircularReferences;

use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class Ex07_BreakingCircularReferencesTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();
        $schema->registerProvider(OrderTypeProvider::class);
        $this->phodam = $schema->getPhodam();
    }

    public function testCreateOrder(): void
    {
        $order = $this->phodam->create(Order::class);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertIsInt($order->getId());
        $this->assertIsArray($order->getItems());
        foreach ($order->getItems() as $item) {
            $this->assertInstanceOf(OrderItem::class, $item);
            $this->assertInstanceOf(Order::class, $item->getOrder());
            $this->assertSame($order, $item->getOrder());
        }
    }
}
