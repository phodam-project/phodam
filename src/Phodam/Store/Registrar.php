<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Store;

use InvalidArgumentException;
use Phodam\Provider\DefinitionBasedTypeProvider;
use Phodam\Provider\ProviderInterface;
use Phodam\Types\TypeDefinition;
use ReflectionClass;

class Registrar implements RegistrarInterface
{
    private ProviderStoreInterface $store;

    private ?string $type = null;

    private ?string $name = null;

    private bool $overriding = false;

    public function __construct(ProviderStoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * @return $this
     */
    public function withType(string $type)
    {
        if (isset($this->type)) {
            throw new InvalidArgumentException('type already set');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return $this
     */
    public function withName(string $name)
    {
        if (isset($this->name)) {
            throw new InvalidArgumentException('name already set');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return $this
     */
    public function overriding()
    {
        $this->overriding = true;

        return $this;
    }

    /**
     * @param ProviderInterface | class-string<ProviderInterface> $providerOrClass
     */
    public function registerProvider($providerOrClass): void
    {
        if (!isset($this->type)) {
            throw new InvalidArgumentException('type not set');
        }

        if ($providerOrClass instanceof ProviderInterface) {
            $provider = $providerOrClass;
        } else {
            $provider = (new ReflectionClass($providerOrClass))->newInstance();

            if (!($provider instanceof ProviderInterface)) {
                throw new InvalidArgumentException(
                    "Argument must be an instance of ProviderInterface or a class implementing it"
                );
            }
        }

        if (isset($this->name)) {
            if ($this->overriding) {
                $this->store->deregisterNamedProvider($this->type, $this->name);
            }

            $this->store->registerNamedProvider($this->type, $this->name, $provider);
        } else {
            if ($this->overriding) {
                $this->store->deregisterDefaultProvider($this->type);
            }

            $this->store->registerDefaultProvider($this->type, $provider);
        }
    }

    /**
     * @param TypeDefinition $definition
     */
    public function registerDefinition(TypeDefinition $definition): void
    {
        if (!isset($this->type)) {
            throw new InvalidArgumentException('type not set');
        }

        $this->registerProvider(
            new DefinitionBasedTypeProvider($this->type, $definition)
        );
    }
}
