<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use Phodam\PhodamInterface;

class ProviderContext implements PhodamInterface
{
    private PhodamInterface $phodam;

    private string $type;

    /** @var array<string, mixed> */
    private array $overrides;

    /** @var array<string, mixed> */
    private array $config;

    /**
     * @param PhodamInterface $phodam a Phodam instance to create new values
     * @param string $type the type to be created
     * @param array<string, mixed> $overrides values to override
     * @param array<string, mixed> $config provider-specific information. An
     *     open-ended array for the provider to pass information along
     */
    public function __construct(
        PhodamInterface $phodam,
        string $type,
        array $overrides,
        array $config
    ) {
        $this->phodam = $phodam;
        $this->type = $type;
        $this->overrides = $overrides;
        $this->config = $config;
    }

    /**
       Return the type to be created by the provider.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return an array of overrides for specific fields for the value created by
     * the provider.
     *
     * @return array<string, mixed>
     */
    public function getOverrides(): array
    {
        return $this->overrides;
    }

    /**
     * Return whether the given field is overridden in this context.
     *
     * @param string $field the field name to check
     * @return bool whether the given field is overridden
     */
    public function hasOverride(string $field): bool
    {
        return isset($this->overrides[$field]);
    }

    /**
     * Return the override value for the given field in this context, or null if
     * the field is not overridden.
     *
     * @param string $field the field name to check
     * @return mixed the overridden value
     */
    public function getOverride(string $field): mixed
    {
        return $this->overrides[$field] ?? null;
    }

    /**
     * Return provider-specific information. An open-ended array for the
     * provider to pass information along.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function createArray(
        string $name,
        ?array $overrides = null,
        ?array $config = null
    ): array {
        return $this->phodam->createArray($name, $overrides, $config);
    }

    /**
     * @inheritDoc
     */
    public function create(
        string $type,
        ?string $name = null,
        ?array $overrides = null,
        ?array $config = null
    ): mixed {
        return $this->phodam->create($type, $name, $overrides, $config);
    }
}
