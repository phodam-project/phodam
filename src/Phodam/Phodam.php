<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use Phodam\Analyzer\TypeAnalyzer;
use Phodam\Analyzer\TypeDefinition;
use Phodam\Provider\Builtin\DefaultDateTimeImmutableTypeProvider;
use Phodam\Provider\Builtin\DefaultDateTimeTypeProvider;
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
            // TODO: This is replaced by `PhodamSchema::withDefaults`
            $this->registerPrimitiveTypeProviders();
            $this->registerBuiltinTypeProviders();
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
            $definition = $this->typeAnalyzer->analyze($type);
            $provider = $this->registerTypeDefinition($type, $definition);
        }

        $context = new ProviderContext($this, $type, $overrides ?? [], $config ?? []);

        try {
            return $provider->create($context);
        } catch (Throwable $ex) {
            throw new CreationFailedException($type, $name, null, $ex);
        }
    }

    /**
     * @param string $type
     * @param TypeDefinition $definition
     * @return ProviderInterface
     */
    public function registerTypeDefinition(string $type, TypeDefinition $definition): ProviderInterface
    {
        $provider = new DefinitionBasedTypeProvider($type, $definition);
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

    private function registerPrimitiveTypeProviders(): void
    {
        // register default providers
        $this->registerProviderConfig(
            (new ProviderConfig(new DefaultStringTypeProvider()))->forType('string')
        );
        $this->registerProviderConfig(
            (new ProviderConfig(new DefaultIntTypeProvider()))->forType('int')
        );
        $this->registerProviderConfig(
            (new ProviderConfig(new DefaultFloatTypeProvider()))->forType('float')
        );
        $this->registerProviderConfig(
            (new ProviderConfig(new DefaultBoolTypeProvider()))->forType('bool')
        );
    }

    // TODO: Figure out two things
    //     1. Where we want to register all of these other 'Builtin' classes, because we should
    //     2. Figure out a way to make interface/abstract -> instantiable mappings
    //        in case the field is only an interface/abstract
    private function registerBuiltinTypeProviders(): void
    {
        // register default providers
        $this->registerProviderConfig(
            (new ProviderConfig(new DefaultDateTimeTypeProvider()))->forType(DateTime::class)
        );

        $this->registerProviderConfig(
            (new ProviderConfig(new DefaultDateTimeImmutableTypeProvider()))->forType(DateTimeImmutable::class)
        );
    }
}
