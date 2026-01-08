<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace Phodam\Store;

use Phodam\Provider\ProviderInterface;
use Phodam\Types\TypeNormalizer;

class ProviderStore implements ProviderStoreInterface
{
    /**
     * A map of type-string => { provider-name => ProviderInterface }
     * @var array<string, array<string, ProviderInterface>>
     */
    private array $namedProviders = [];

    /**
     * A map of type-string => ProviderInterface
     * @var array<string, ProviderInterface>
     */
    private array $defaultProviders = [];

    /**
     * @inheritDoc
     */
    public function hasNamedProvider(string $type, string $name): bool
    {
        $type = TypeNormalizer::normalize($type);

        return isset($this->namedProviders[$type][$name]);
    }

    /**
     * @inheritDoc
     */
    public function hasDefaultProvider(string $type): bool
    {
        $type = TypeNormalizer::normalize($type);

        return isset($this->defaultProviders[$type]);
    }

    /**
     * @inheritDoc
     */
    public function findNamedProvider(string $type, string $name): ProviderInterface
    {
        $type = TypeNormalizer::normalize($type);

        if (!isset($this->namedProviders[$type][$name])) {
            throw new ProviderNotFoundException(
                "No provider found for type {$type} with name {$name}"
            );
        }

        return $this->namedProviders[$type][$name];
    }

    /**
     * @inheritDoc
     */
    public function findDefaultProvider(string $type): ProviderInterface
    {
        $type = TypeNormalizer::normalize($type);

        if (!isset($this->defaultProviders[$type])) {
            throw new ProviderNotFoundException(
                "No default provider found for type {$type}"
            );
        }

        return $this->defaultProviders[$type];
    }

    /**
     * @inheritDoc
     */
    public function findAllNamedProvidersForType(string $type): array
    {
        $type = TypeNormalizer::normalize($type);

        return $this->namedProviders[$type] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function registerNamedProvider(
        string $type,
        string $name,
        ProviderInterface $provider
    ): void {
        $type = TypeNormalizer::normalize($type);

        if (isset($this->namedProviders[$type][$name])) {
            throw new ProviderConflictException(
                "Provider for type {$type} with name {$name} already registered"
            );
        }

        $this->namedProviders[$type] ??= [];
        $this->namedProviders[$type][$name] = $provider;
    }

    /**
     * @inheritDoc
     */
    public function registerDefaultProvider(
        string $type,
        ProviderInterface $provider
    ): void {
        $type = TypeNormalizer::normalize($type);

        if ($type === 'array') {
            // TODO: Does this deserve its own exception type? These exceptions
            // will usually be programmer error, so they shouldn't be caught
            // anyway.
            throw new ProviderConflictException(
                "Array providers must be registered with a name"
            );
        }

        if (isset($this->defaultProviders[$type])) {
            throw new ProviderConflictException(
                "Default provider for type {$type} already registered"
            );
        }

        $this->defaultProviders[$type] = $provider;
    }

    /**
     * @inheritDoc
     */
    public function deregisterNamedProvider(string $type, string $name): void
    {
        $type = TypeNormalizer::normalize($type);

        if (!isset($this->namedProviders[$type][$name])) {
            throw new ProviderNotFoundException(
                "No provider found for type {$type} with name {$name}"
            );
        }

        unset($this->namedProviders[$type][$name]);
    }

    /**
     * @inheritDoc
     */
    public function deregisterDefaultProvider(string $type): void
    {
        $type = TypeNormalizer::normalize($type);

        if (!isset($this->defaultProviders[$type])) {
            throw new ProviderNotFoundException(
                "No default provider found for type {$type}"
            );
        }

        unset($this->defaultProviders[$type]);
    }
}
