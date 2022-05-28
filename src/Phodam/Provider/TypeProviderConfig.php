<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use InvalidArgumentException;
use Phodam\PhodamTypes;

class TypeProviderConfig
{
    private ?string $name = null;
    private ?string $class = null;
    private ?string $primitive = null;
    private bool $array = false;
    private TypeProviderInterface $typeProvider;

    /**
     * @param TypeProviderInterface $typeProvider
     */
    public function __construct(TypeProviderInterface $typeProvider)
    {
        $this->typeProvider = $typeProvider;
    }

    /**
     * Validates the configuration and throws an Exception if it's not valid
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        if (!$this->isArray() && !$this->getPrimitive() && !$this->getClass()) {
            throw new InvalidArgumentException(
                "A provider config must be declared for an array, primitive, or a class"
            );
        }

        if ($this->isArray() && !$this->getName()) {
            throw new InvalidArgumentException(
                "An array provider config must have a name"
            );
        }
    }

    /**
     * Sets up the config for an array type provider
     *
     * @return $this
     */
    public function forArray(): self
    {
        $this->array = true;
        $this->primitive = null;
        $this->class = null;
        return $this;
    }

    /**
     * Sets up the config for a class type provider
     *
     * @param string $class the class
     * @return $this
     */
    public function forClass(string $class): self
    {
        $this->array = false;
        $this->primitive = null;
        $this->class = $class;
        return $this;
    }

    /**
     * Sets the config up for a float primitive type provider
     *
     * @return $this
     */
    public function forFloat(): self
    {
        $this->setPrimitive(PhodamTypes::PRIMITIVE_FLOAT);
        return $this;
    }

    /**
     * Sets the config up for a int primitive type provider
     *
     * @return $this
     */
    public function forInt(): self
    {
        $this->setPrimitive(PhodamTypes::PRIMITIVE_INT);
        return $this;
    }

    /**
     * Sets the config up for a string primitive type provider
     *
     * @return $this
     */
    public function forString(): self
    {
        $this->setPrimitive(PhodamTypes::PRIMITIVE_STRING);
        return $this;
    }

    /**
     * Adds a name to the provider
     *
     * @param string $name a custom name to the provider
     * @return $this
     */
    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function getPrimitive(): ?string
    {
        return $this->primitive;
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->array;
    }

    /**
     * @return TypeProviderInterface
     */
    public function getTypeProvider(): TypeProviderInterface
    {
        return $this->typeProvider;
    }

    /**
     * Helper method to unset other values and set a primitive type provider up
     *
     * @param string $primitive the primitive type for this provider
     * @return void
     */
    private function setPrimitive(string $primitive): void
    {
        $this->array = false;
        $this->primitive = $primitive;
        $this->class = null;
    }
}
