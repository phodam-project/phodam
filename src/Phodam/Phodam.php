<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace Phodam;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use DatePeriod;
use InvalidArgumentException;
use Phodam\Analyzer\TypeAnalyzer;
use Phodam\Types\TypeDefinition;
use Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeTypeProvider;
use Phodam\Provider\Builtin\DefaultDateIntervalTypeProvider;
use Phodam\Provider\Builtin\DefaultDatePeriodTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeZoneTypeProvider;
use Phodam\Provider\Builtin\DefaultEnumTypeProvider;
use Phodam\Provider\CreationFailedException;
use Phodam\Provider\DefinitionBasedTypeProvider;
use Phodam\Provider\Primitive\DefaultBoolTypeProvider;
use Phodam\Provider\Primitive\DefaultFloatTypeProvider;
use Phodam\Provider\Primitive\DefaultIntTypeProvider;
use Phodam\Provider\Primitive\DefaultStringTypeProvider;
use Phodam\Provider\ProviderConfig;
use Phodam\Provider\ProviderContext;
use Phodam\Provider\ProviderInterface;
use Phodam\Store\ProviderConflictException;
use Phodam\Store\ProviderNotFoundException;
use Phodam\Store\ProviderStore;
use ReflectionClass;
use Throwable;

class Phodam implements PhodamInterface
{
    private ProviderStore $providerStore;

    private TypeAnalyzer $typeAnalyzer;

    public function __construct(?ProviderStore $providerStore = null)
    {
        if ($providerStore !== null) {
            $this->providerStore = $providerStore;
        } else {
            // Fallback for now, but soon we'll require ProviderStore.
            $this->providerStore = new ProviderStore();
        }

        $this->typeAnalyzer = new TypeAnalyzer();
    }

    /**
     * @inheritDoc
     */
    public function createArray(
        string $name,
        ?array $overrides = null,
        ?array $config = null
    ): array {
        $provider = $this->getArrayProvider($name);
        $context = new ProviderContext($this, 'array', $overrides ?? [], $config ?? []);

        try {
            return $provider->create($context);
        } catch (Throwable $ex) {
            throw new CreationFailedException('array', $name, null, $ex);
        }
    }

    /**
     * @inheritDoc
     */
    public function create(
        string $type,
        ?string $name = null,
        ?array $overrides = null,
        ?array $config = null
    ) {
        try {
            $provider = $this
                ->getTypeProvider($type, $name);
        } catch (ProviderNotFoundException $ex) {
            // Check if the type is an enum before trying to analyze it
            if ($this->isEnum($type)) {
                $provider = $this->getOrRegisterEnumProvider($type);
            } else {
                $definition = $this->typeAnalyzer->analyze($type);
                $provider = $this->registerTypeDefinition($definition);
            }
        }

        $context = new ProviderContext(
            $this,
            $type,
            $overrides ?? [],
            $config ?? []
        );

        try {
            return $provider->create($context);
        } catch (Throwable $ex) {
            throw new CreationFailedException($type, $name, null, $ex);
        }
    }

    /**
     * @param TypeDefinition<*> $definition
     * @return ProviderInterface
     */
    public function registerTypeDefinition(TypeDefinition $definition): ProviderInterface
    {
        $type = $definition->getType();

        if ($type == null || $type == '') {
            throw new InvalidArgumentException('TypeDefinition must have a type set');
        }

        $provider = new DefinitionBasedTypeProvider($definition);
        $providerConfig = (new ProviderConfig($provider))
            ->forType($type);
        $this->registerProviderConfig($providerConfig);

        return $provider;
    }

    /**
     * Registers a TypeProvider using a ProviderConfig
     *
     * @param ProviderConfig $config
     * @return self
     */
    public function registerProviderConfig(ProviderConfig $config): self
    {
        $config->validate();

        $isArray = $config->isArray();
        $type = $config->getType();

        if ($isArray) {
            $this->registerArrayProviderConfig($config);
            return $this;
        }

        if ($type) {
            $this->registerTypeProviderConfig($config);
            return $this;
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
        return $this->providerStore->findNamedProvider('array', $name);
    }

    /**
     * Returns a type provider by type name and optionally name
     *
     * @param string $type
     * @param string|null $name
     * @return ProviderInterface
     * @throws ProviderNotFoundException if a provider can't be found
     */
    public function getTypeProvider(string $type, ?string $name = null): ProviderInterface
    {
        if ($name) {
            // We're looking for a named provider.
            $provider = $this->providerStore->findNamedProvider($type, $name);
        } else {
            // We're looking for a default provider.
            $provider = $this->providerStore->findDefaultProvider($type);
        }

        return $provider;
    }

    /**
     * Registers a ProviderConfig for an array
     *
     * @param ProviderConfig $config
     * @return void
     * @throws ProviderConflictException
     */
    private function registerArrayProviderConfig(ProviderConfig $config): void
    {
        $name = $config->getName();
        $provider = $config->getProvider();

        $this->providerStore->registerNamedProvider('array', $name, $provider);
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

        if ($name) {
            $this->providerStore->registerNamedProvider($type, $name, $provider);
        } else {
            $this->providerStore->registerDefaultProvider($type, $provider);
        }
    }

    /**
     * Checks if a given type is a PHP 8 enum
     *
     * @param string $type
     * @return bool
     */
    private function isEnum(string $type): bool
    {
        if (!class_exists($type) && !enum_exists($type)) {
            return false;
        }

        try {
            $reflection = new ReflectionClass($type);
            return $reflection->isEnum();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * Gets or registers an enum provider for the given enum type
     *
     * @param string $type The enum type
     * @return ProviderInterface
     */
    private function getOrRegisterEnumProvider(string $type): ProviderInterface
    {
        // Check if we already have a provider for this enum
        try {
            return $this->providerStore->findDefaultProvider($type);
        } catch (ProviderNotFoundException $ex) {
            // Provider doesn't exist, create and register it
            $provider = new DefaultEnumTypeProvider();
            $providerConfig = (new ProviderConfig($provider))
                ->forType($type);
            $this->registerProviderConfig($providerConfig);

            return $provider;
        }
    }
}
