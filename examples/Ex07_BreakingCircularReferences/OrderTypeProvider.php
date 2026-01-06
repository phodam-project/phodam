<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex07_BreakingCircularReferences;

use Phodam\Provider\PhodamProvider;
use Phodam\Provider\TypedProviderInterface;
use Phodam\Provider\ProviderContextInterface;

/**
 * @template T extends Order
 * @template-implements TypedProviderInterface<Order>
 */
#[PhodamProvider(Order::class)]
class OrderTypeProvider implements TypedProviderInterface
{
    public function create(ProviderContextInterface $context): Order
    {
        $order = new Order();

        $numItems = $context->getConfig()['numItems'] ?? 2;

        $defaults = [
            'id' => $context->getPhodam()->create('int'),
            'items' => array_map(
                fn ($i) => $context->getPhodam()->create(OrderItem::class, null, ['order' => $order]),
                range(0, $numItems)
            ),
        ];

        $values = array_merge($defaults, $context->getOverrides());

        return $order
            ->setId($values['id'])
            ->setItems($values['items']);
    }
}
