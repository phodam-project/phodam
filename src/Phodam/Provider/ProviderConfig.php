<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam\Provider;

use InvalidArgumentException;

class ProviderConfig
{
    private ?string $name = null;
    private ?string $type = null;
    private bool $array = false;
    private ProviderInterface $provider;

    /**
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Validates the configuration and throws an Exception if it's not valid
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        if (!$this->isArray() && !$this->getType()) {
            throw new InvalidArgumentException(
                "A provider config must be declared for an array or a type"
            );
        }

        if ($this->isArray() && !$this->getName()) {
            throw new InvalidArgumentException(
                "An array provider config must have a name"
            );
        }
    }

    /**
     * Sets up the config for an array provider
     *
     * @return $this
     */
    public function forArray(): self
    {
        $this->array = true;
        $this->type = null;
        return $this;
    }

    /**
     * Sets up the config for a type provider
     *
     * @param string $type the type
     * @return $this
     */
    public function forType(string $type): self
    {
        $this->array = false;
        $this->type = $type;
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
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->array;
    }

    /**
     * @return ProviderInterface
     */
    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }
}
