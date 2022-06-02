<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

use InvalidArgumentException;
use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use Phodam\Provider\Primitive\DefaultFloatTypeProvider;
use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Provider\ProviderConfig;
use Phodam\Provider\ProviderInterface;

class Phodam implements PhodamInterface
{
    /**
     * A map of array-provider-name => ProviderInterface
     * @var array<string, ProviderInterface>
     */
    private array $arrayProviders = [];

    /**
     * A map of class-string => ProviderInterface
     * @var array<string, ProviderInterface>
     */
    private array $providers = [];

    /**
     * A map of class-string => { provider-name => ProviderInterface }
     * @var array<string, array<string, ProviderInterface>>
     */
    private array $namedProviders = [];

    public function __construct()
    {
        $this->registerPrimitiveTypeProviders();
    }

    /**
     * @inheritDoc
     */
    public function createArray(
        string $name,
        array $overrides = [],
        array $config = []
    ): array {
        return $this
            ->getArrayProvider($name)
            ->create($overrides, $config);
    }

    /**
     * @inheritDoc
     */
    public function create(
        string $type,
        string $name = null,
        array $overrides = [],
        array $config = []
    ) {
        return $this
            ->getTypeProvider($type, $name)
            ->create($overrides, $config);
    }

    /**
     * Registers a TypeProvider using a ProviderConfig
     *
     * @param ProviderConfig $config
     * @return void
     */
    public function registerProviderConfig(ProviderConfig $config)
    {
        $config->validate();

        $isArray = $config->isArray();
        $type = $config->getType();

        if ($isArray) {
            $this->registerArrayProviderConfig($config);
            return;
        }

        if ($type) {
            $this->registerTypeProviderConfig($config);
            return;
        }

        // $config->validate() should always prevent us from getting here
        throw new InvalidArgumentException(
            "Unable to determine how to register type provider"
        );
    }

    /**
     * Returns an array provider
     *
     * @param string $name the name of the array provider
     * @return ProviderInterface
     */
    public function getArrayProvider(string $name): ProviderInterface
    {
        if (!array_key_exists($name, $this->arrayProviders)) {
            throw new InvalidArgumentException(
                "Unable to find an array provider with the name {$name}"
            );
        }

        return $this->arrayProviders[$name];
    }

    /**
     * Returns a type provider by type name and optionally name
     *
     * @template T
     * @param class-string<T> $type
     * @param string|null $name
     * @return ProviderInterface
     */
    public function getTypeProvider(string $type, ?string $name = null): ProviderInterface
    {
        // we're looking for a named provider
        if ($name) {
            if (
                !array_key_exists($type, $this->namedProviders)
                || !array_key_exists($name, $this->namedProviders[$type])
            ) {
                throw new InvalidArgumentException(
                    "Unable to find a provider of type {$type} with the name {$name}"
                );
            }
            return $this->namedProviders[$type][$name];
        } else {
            // looking for a default provider
            if (!array_key_exists($type, $this->providers)) {
                throw new InvalidArgumentException(
                    "Unable to find a default provider of type {$type}"
                );
            }
            return $this->providers[$type];
        }
    }

    /**
     * Registers a ProviderConfig for an array
     *
     * @param ProviderConfig $config
     * @return void
     */
    private function registerArrayProviderConfig(ProviderConfig $config): void
    {
        if (array_key_exists($config->getName(), $this->arrayProviders)) {
            throw new InvalidArgumentException(
                "An array provider with the name {$config->getName()} already exists"
            );
        }

        $provider = $config->getProvider();
        if ($provider instanceof PhodamAware) {
            $provider->setPhodam($this);
        }

        $this->arrayProviders[$config->getName()] = $config->getProvider();
    }

    /**
     * Registers a ProviderConfig for a class
     *
     * @param ProviderConfig $config
     * @return void
     */
    private function registerTypeProviderConfig(ProviderConfig $config): void
    {
        $type = $config->getType();
        $name = $config->getName();

        $provider = $config->getProvider();
        if ($provider instanceof PhodamAware) {
            $provider->setPhodam($this);
        }

        if ($name) {
            // create the named type array if it doesn't exist
            if (!array_key_exists($type, $this->namedProviders)) {
                $this->namedProviders[$type] = [];
            }

            // check that we don't have a named provider for this type
            if (array_key_exists($name, $this->namedProviders[$type])) {
                throw new InvalidArgumentException(
                    "A type provider of type {$type} with the name {$name} already exists"
                );
            }
            $this->namedProviders[$type][$name] = $provider;
        } else {
            $this->providers[$type] = $provider;
        }
    }

    private function registerPrimitiveTypeProviders(): void
    {
        // register default providers
        $stringProviderConfig =
            (new ProviderConfig(new DefaultStringTypeProvider()))
                ->forType('string');
        $this->registerProviderConfig($stringProviderConfig);

        $intProviderConfig =
            (new ProviderConfig(new DefaultIntTypeProvider()))
                ->forType('int');
        $this->registerProviderConfig($intProviderConfig);

        $floatProviderConfig =
            (new ProviderConfig(new DefaultFloatTypeProvider()))
                ->forType('float');
        $this->registerProviderConfig($floatProviderConfig);

        $boolProviderConfig =
            (new ProviderConfig(new DefaultBoolTypeProvider()))
                ->forType('bool');
        $this->registerProviderConfig($boolProviderConfig);
    }
}
