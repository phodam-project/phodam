<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex05_PHPDocTypeDetection;

use DateTimeImmutable;

/**
 * Example class with mixed typed and untyped properties
 * Phodam uses type declarations where available, PHPDoc for the rest
 */
class MixedClass
{
    private int $id;  // Typed - auto-detected

    /** @var string */
    private $name;  // Untyped - uses PHPDoc

    private ?string $email;  // Typed nullable - auto-detected

    /** @var float */
    private $balance;  // Untyped - uses PHPDoc

    private bool $active;  // Typed - auto-detected

    /** @var Address */
    private $address;  // Untyped - uses PHPDoc

    private DateTimeImmutable $createdAt;  // Typed - auto-detected

    /** @var Order[] */
    private $orders;  // Untyped array - uses PHPDoc for element type

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): MixedClass
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): MixedClass
    {
        $this->email = $email;
        return $this;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): MixedClass
    {
        $this->active = $active;
        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): MixedClass
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Order[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param Order[] $orders
     * @return MixedClass
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
        return $this;
    }
}

