<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex04_DefinitionBasedProviders;

use DateTimeImmutable;

/**
 * Example class with untyped properties that require definition-based providers
 */
class Product
{
    private $id;              // Untyped - needs definition
    private string $name;     // Typed - auto-detected
    private $description;     // Untyped - needs definition
    private ?string $sku;     // Nullable typed - auto-detected
    private array $tags;      // Array without element type - needs definition
    private $price;          // Untyped - needs definition with config
    private DateTimeImmutable $createdAt;  // Typed - auto-detected
    private bool $inStock;    // Typed - auto-detected

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): Product
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): Product
    {
        $this->description = $description;
        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): Product
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return Product
     */
    public function setTags(array $tags): Product
    {
        $this->tags = $tags;
        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): Product
    {
        $this->price = $price;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): Product
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isInStock(): bool
    {
        return $this->inStock;
    }

    public function setInStock(bool $inStock): Product
    {
        $this->inStock = $inStock;
        return $this;
    }
}

