<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Analyzer;

class FieldDefinition
{
    private string $type;
    private ?string $name = null;
    /** @var array<string, mixed>|null $config */
    private ?array $config = [];
    /** @var array<string, mixed>|null $overrides */
    private ?array $overrides = [];
    private bool $nullable = false;
    private bool $array = false;

    /**
     * @param array<string, mixed> $definition
     * @return self
     */
    public static function fromArray(array $definition): self
    {
        $def = new FieldDefinition($definition['type']);
        if (isset($definition['name'])) {
            $def->setName($definition['name']);
        }
        if (isset($definition['config'])) {
            $def->setConfig($definition['config']);
        }
        if (isset($definition['overrides'])) {
            $def->setOverrides($definition['overrides']);
        }
        if (isset($definition['nullable'])) {
            $def->setNullable($definition['nullable']);
        }
        if (isset($definition['array'])) {
            $def->setArray($definition['array']);
        }
        return $def;
    }

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return FieldDefinition
     */
    public function setName(?string $name): FieldDefinition
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * @param array<string, mixed>|null $config
     * @return FieldDefinition
     */
    public function setConfig(?array $config): FieldDefinition
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getOverrides(): ?array
    {
        return $this->overrides;
    }

    /**
     * @param array<string, mixed>|null $overrides
     * @return FieldDefinition
     */
    public function setOverrides(?array $overrides): FieldDefinition
    {
        $this->overrides = $overrides;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     * @return FieldDefinition
     */
    public function setNullable(bool $nullable): FieldDefinition
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isArray(): bool
    {
        return $this->array;
    }

    /**
     * @param bool $array
     * @return FieldDefinition
     */
    public function setArray(bool $array): FieldDefinition
    {
        $this->array = $array;
        return $this;
    }
}
