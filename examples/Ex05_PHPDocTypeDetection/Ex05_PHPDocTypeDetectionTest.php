<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex05_PHPDocTypeDetection;

use DateTimeImmutable;
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class Ex05_PHPDocTypeDetectionTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();
        $this->phodam = $schema->getPhodam();
    }

    public function testLegacyClassWithPHPDoc(): void
    {
        // LegacyUser has no typed properties, only PHPDoc @var annotations
        // Phodam automatically detects types from PHPDoc
        $user = $this->phodam->create(LegacyUser::class);

        $this->assertInstanceOf(LegacyUser::class, $user);
        $this->assertIsInt($user->getId());  // From @var int
        $this->assertIsString($user->getFirstName());  // From @var string
        $this->assertIsString($user->getLastName());  // From @var string
        $this->assertIsString($user->getEmail());  // From @var string
        $this->assertIsBool($user->isActive());  // From @var bool
        $this->assertIsFloat($user->getBalance());  // From @var float
        $this->assertInstanceOf(Address::class, $user->getAddress());  // From @var Address
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getCreatedAt());  // From @var DateTimeImmutable
        $this->assertIsArray($user->getOrders());  // From @var Order[]

        // Verify orders array contains Order objects
        foreach ($user->getOrders() as $order) {
            $this->assertInstanceOf(Order::class, $order);
        }
    }

    public function testArrayTypesFromPHPDoc(): void
    {
        // PHPDoc can specify array element types
        $user = $this->phodam->create(LegacyUser::class);

        // @var Order[] tells Phodam the array contains Order objects
        $orders = $user->getOrders();
        $this->assertIsArray($orders);
        $this->assertGreaterThanOrEqual(2, count($orders));
        $this->assertLessThanOrEqual(5, count($orders));

        foreach ($orders as $order) {
            $this->assertInstanceOf(Order::class, $order);
            $this->assertIsInt($order->getOrderId());
            $this->assertIsString($order->getOrderNumber());
            $this->assertIsFloat($order->getTotal());
            $this->assertInstanceOf(DateTimeImmutable::class, $order->getOrderDate());
            $this->assertIsArray($order->getItems());

            // Order also has array with PHPDoc
            foreach ($order->getItems() as $item) {
                $this->assertInstanceOf(OrderItem::class, $item);
            }
        }
    }

    public function testNestedLegacyObjects(): void
    {
        // Legacy objects can contain other legacy objects
        $user = $this->phodam->create(LegacyUser::class);

        // Address is also a legacy class with PHPDoc
        $address = $user->getAddress();
        $this->assertInstanceOf(Address::class, $address);
        $this->assertIsString($address->getStreet());  // From @var string
        $this->assertIsString($address->getCity());  // From @var string
        $this->assertIsString($address->getState());  // From @var string
        $this->assertIsString($address->getZipCode());  // From @var string
    }

    public function testMixedTypedAndUntypedProperties(): void
    {
        // MixedClass has both typed properties and untyped with PHPDoc
        // Phodam uses type declarations when available, PHPDoc otherwise
        $mixed = $this->phodam->create(MixedClass::class);

        // Typed properties are auto-detected
        $this->assertIsInt($mixed->getId());  // From type declaration
        $this->assertIsString($mixed->getEmail());  // From ?string type (nullable)
        $this->assertIsBool($mixed->isActive());  // From bool type
        $this->assertInstanceOf(DateTimeImmutable::class, $mixed->getCreatedAt());  // From type declaration

        // Untyped properties use PHPDoc
        $this->assertIsString($mixed->getName());  // From @var string
        $this->assertIsFloat($mixed->getBalance());  // From @var float
        $this->assertInstanceOf(Address::class, $mixed->getAddress());  // From @var Address
        $this->assertIsArray($mixed->getOrders());  // From @var Order[]

        // Verify orders array
        foreach ($mixed->getOrders() as $order) {
            $this->assertInstanceOf(Order::class, $order);
        }
    }

    public function testLegacyClassWithOverrides(): void
    {
        // PHPDoc-detected classes work with overrides just like typed classes
        $user = $this->phodam->create(LegacyUser::class, overrides: [
            'id' => 999,
            'firstName' => 'Jane',
            'lastName' => 'Smith',
            'email' => 'jane@example.com',
            'active' => true
        ]);

        $this->assertEquals(999, $user->getId());
        $this->assertEquals('Jane', $user->getFirstName());
        $this->assertEquals('Smith', $user->getLastName());
        $this->assertEquals('jane@example.com', $user->getEmail());
        $this->assertTrue($user->isActive());

        // Other fields are still generated
        $this->assertIsFloat($user->getBalance());
        $this->assertInstanceOf(Address::class, $user->getAddress());
    }

    public function testNestedOverridesWithPHPDoc(): void
    {
        // For nested overrides, we need to create the nested objects explicitly
        // or use a custom provider that handles nested overrides
        $address = $this->phodam->create(Address::class, overrides: [
            'city' => 'Boston',
            'state' => 'MA'
        ]);

        $order = $this->phodam->create(Order::class, overrides: [
            'orderNumber' => 'ORD-12345',
            'total' => 199.99
        ]);

        $user = $this->phodam->create(LegacyUser::class, overrides: [
            'address' => $address,
            'orders' => [$order]
        ]);

        $this->assertEquals('Boston', $user->getAddress()->getCity());
        $this->assertEquals('MA', $user->getAddress()->getState());

        $orders = $user->getOrders();
        $this->assertGreaterThan(0, count($orders));
        $this->assertEquals('ORD-12345', $orders[0]->getOrderNumber());
        $this->assertEquals(199.99, $orders[0]->getTotal());
    }

    public function testComplexLegacyObjectGraph(): void
    {
        // Create a complete object graph using only PHPDoc
        $user = $this->phodam->create(LegacyUser::class);

        // Verify all properties are populated correctly
        $this->assertIsInt($user->getId());
        $this->assertIsString($user->getFirstName());
        $this->assertIsString($user->getLastName());
        $this->assertIsString($user->getEmail());
        $this->assertIsBool($user->isActive());
        $this->assertIsFloat($user->getBalance());

        // Nested object
        $address = $user->getAddress();
        $this->assertInstanceOf(Address::class, $address);
        $this->assertIsString($address->getStreet());
        $this->assertIsString($address->getCity());
        $this->assertIsString($address->getState());
        $this->assertIsString($address->getZipCode());

        // Array of objects
        $orders = $user->getOrders();
        $this->assertIsArray($orders);
        foreach ($orders as $order) {
            $this->assertInstanceOf(Order::class, $order);
            $this->assertIsInt($order->getOrderId());
            $this->assertIsString($order->getOrderNumber());
            $this->assertIsFloat($order->getTotal());
            $this->assertInstanceOf(DateTimeImmutable::class, $order->getOrderDate());

            // Nested array
            $items = $order->getItems();
            $this->assertIsArray($items);
            foreach ($items as $item) {
                $this->assertInstanceOf(OrderItem::class, $item);
                $this->assertIsInt($item->getItemId());
                $this->assertIsString($item->getProductName());
                $this->assertIsInt($item->getQuantity());
                $this->assertIsFloat($item->getPrice());
            }
        }
    }
}

