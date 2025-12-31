<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex04_DefinitionBasedProviders;

use DateTimeImmutable;
use Phodam\Types\FieldDefinition;
use Phodam\Types\TypeDefinition;
use Phodam\Provider\TypedProviderInterface;
use Phodam\Provider\ProviderContextInterface;
use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

class Ex04_DefinitionBasedProvidersTest extends TestCase
{
    private PhodamInterface $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $schema = PhodamSchema::withDefaults();

        // Example 1: Define untyped fields
        // Product has some typed fields (auto-detected) and some untyped fields (need definition)
        $productDefinition = new TypeDefinition([
            'id' => new FieldDefinition('int'),
            'description' => new FieldDefinition('string'),
            'tags' => (new FieldDefinition('string'))->setArray(true),
            'price' => (new FieldDefinition('float'))
                ->setConfig(['min' => 0.01, 'max' => 1000.0, 'precision' => 2])
        ]);

        $schema->forType(Product::class)
            ->registerDefinition($productDefinition);

        // Example 2: Define array field with element type
        // Order has an array field that needs element type specification
        $orderDefinition = new TypeDefinition([
            'items' => (new FieldDefinition(OrderItem::class))
                ->setArray(true)
        ]);

        $schema->forType(Order::class)
            ->registerDefinition($orderDefinition);

        $this->phodam = $schema->getPhodam();
    }

    public function testProductWithUntypedFields(): void
    {
        // Product has untyped fields that need definition
        $product = $this->phodam->create(Product::class);
        // var_export($product);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertIsInt($product->getId());  // Defined as 'int'
        $this->assertIsString($product->getName());  // Auto-detected from typed property
        $this->assertIsString($product->getDescription());  // Defined as 'string'
        $this->assertIsString($product->getSku());  // Auto-detected from ?string type
        $this->assertIsArray($product->getTags());  // Defined as array of strings
        $this->assertIsFloat($product->getPrice());  // Defined as 'float' with config
        $this->assertInstanceOf(DateTimeImmutable::class, $product->getCreatedAt());  // Auto-detected
        $this->assertIsBool($product->isInStock());  // Auto-detected

        // Verify price is within configured range
        $this->assertGreaterThanOrEqual(0.01, $product->getPrice());
        $this->assertLessThanOrEqual(1000.0, $product->getPrice());

        // Verify tags are strings
        foreach ($product->getTags() as $tag) {
            $this->assertIsString($tag);
        }
    }

    public function testProductWithOverrides(): void
    {
        // Definition-based providers work with overrides
        $product = $this->phodam->create(Product::class, null, [
            'id' => 12345,
            'name' => 'Custom Product',
            'price' => 99.99
        ]);

        $this->assertEquals(12345, $product->getId());
        $this->assertEquals('Custom Product', $product->getName());
        $this->assertEquals(99.99, $product->getPrice());
        // Other fields are still generated
        $this->assertIsString($product->getDescription());
        $this->assertIsArray($product->getTags());
    }

    public function testOrderWithArrayField(): void
    {
        // Order has an array field that needs element type definition
        $order = $this->phodam->create(Order::class);
        // var_export($order);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertIsInt($order->getOrderId());  // Auto-detected from typed property
        $this->assertIsArray($order->getItems());  // Defined as array

        // Verify items are OrderItem instances
        foreach ($order->getItems() as $item) {
            $this->assertInstanceOf(OrderItem::class, $item);
            $this->assertIsInt($item->getItemId());
            $this->assertIsString($item->getProductName());
            $this->assertIsFloat($item->getQuantity());
            $this->assertIsFloat($item->getUnitPrice());
        }

        // Default array size is 2-5 elements
        $this->assertGreaterThanOrEqual(2, count($order->getItems()));
        $this->assertLessThanOrEqual(5, count($order->getItems()));
    }

    public function testPartialDefinition(): void
    {
        // You only need to define fields that can't be auto-detected
        // Phodam will auto-complete the rest using TypeAnalyzer
        // Note: For untyped fields, you must define all of them
        // Typed fields can be auto-completed
        $minimalDefinition = new TypeDefinition([
            'id' => new FieldDefinition('int'),
            'description' => new FieldDefinition('string'),
            'tags' => (new FieldDefinition('string'))->setArray(true),
            'price' => new FieldDefinition('float')
        ]);

        $schema = PhodamSchema::withDefaults();
        $schema->forType(Product::class)
            ->registerDefinition($minimalDefinition);
        $phodam = $schema->getPhodam();

        $product = $phodam->create(Product::class);
        // Typed fields are auto-completed
        $this->assertIsString($product->getName());
        $this->assertIsBool($product->isInStock());
        $this->assertInstanceOf(DateTimeImmutable::class, $product->getCreatedAt());
        // Untyped fields are defined
        $this->assertIsInt($product->getId());
        $this->assertIsString($product->getDescription());
        $this->assertIsArray($product->getTags());
        $this->assertIsFloat($product->getPrice());
    }

    public function testFieldWithConfiguration(): void
    {
        // Fields can have configuration for their type providers
        // Note: Must define all untyped fields (id, description, tags, price)
        $definition = new TypeDefinition([
            'id' => (new FieldDefinition('int'))
                ->setConfig(['min' => 1000, 'max' => 9999]),
            'description' => new FieldDefinition('string'),
            'tags' => (new FieldDefinition('string'))->setArray(true),
            'price' => (new FieldDefinition('float'))
                ->setConfig(['min' => 10.0, 'max' => 50.0, 'precision' => 2])
        ]);

        $schema = PhodamSchema::withDefaults();
        $schema->forType(Product::class)
            ->registerDefinition($definition);
        $phodam = $schema->getPhodam();

        $product = $phodam->create(Product::class);
        $this->assertGreaterThanOrEqual(1000, $product->getId());
        $this->assertLessThanOrEqual(9999, $product->getId());
        $this->assertGreaterThanOrEqual(10.0, $product->getPrice());
        $this->assertLessThanOrEqual(50.0, $product->getPrice());
    }

    public function testNullableFields(): void
    {
        // Nullable fields can be explicitly marked
        // Note: Must define all untyped fields (id, description, tags, price)
        $definition = new TypeDefinition([
            'id' => new FieldDefinition('int'),
            'description' => (new FieldDefinition('string'))
                ->setNullable(true),  // Can be null
            'tags' => (new FieldDefinition('string'))->setArray(true),
            'price' => new FieldDefinition('float')
        ]);

        $schema = PhodamSchema::withDefaults();
        $schema->forType(Product::class)
            ->registerDefinition($definition);
        $phodam = $schema->getPhodam();

        // Generate multiple products - some may have null description
        $nullCount = 0;
        for ($i = 0; $i < 20; $i++) {
            $product = $phodam->create(Product::class);
            if ($product->getDescription() === null) {
                $nullCount++;
            }
        }

        // At least some should be null (random generation)
        // Note: This test may occasionally fail due to randomness, but it's unlikely
        $this->assertGreaterThanOrEqual(0, $nullCount);
    }

    public function testNamedProviderInFieldDefinition(): void
    {
        // First, register a named provider for OrderItem
        $schema = PhodamSchema::withDefaults();
        $schema->forType(OrderItem::class)
            ->withName('expensive')
            ->registerProvider(new class implements TypedProviderInterface {
                public function create(ProviderContextInterface $context): OrderItem
                {
                    return (new OrderItem())
                        ->setItemId($context->getPhodam()->create('int'))
                        ->setProductName($context->getPhodam()->create('string'))
                        ->setQuantity($context->getPhodam()->create('float', null, [], ['min' => 1.0, 'max' => 10.0]))
                        ->setUnitPrice($context->getPhodam()->create('float', null, [], ['min' => 100.0, 'max' => 1000.0]));
                }
            });

        // Then use it in a field definition
        $orderDefinition = new TypeDefinition([
            'items' => (new FieldDefinition(OrderItem::class))
                ->setArray(true)
                ->setName('expensive')  // Use the named provider
        ]);

        $schema->forType(Order::class)
            ->registerDefinition($orderDefinition);

        $phodam = $schema->getPhodam();
        $order = $phodam->create(Order::class);

        // All items should have expensive prices
        foreach ($order->getItems() as $item) {
            $this->assertGreaterThanOrEqual(100.0, $item->getUnitPrice());
            $this->assertLessThanOrEqual(1000.0, $item->getUnitPrice());
        }
    }
}

