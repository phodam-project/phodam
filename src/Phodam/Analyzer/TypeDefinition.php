<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Analyzer;

class TypeDefinition
{
    /** @var array<string, FieldDefinition> */
    private array $fields;

    /**
     * @param array<string, FieldDefinition> $fields
     */
    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * @return array<string, FieldDefinition>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array<string, FieldDefinition> $fields
     * @return $this
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param string $name
     * @param FieldDefinition $definition
     * @return $this
     */
    public function addField(string $name, FieldDefinition $definition): self
    {
        $this->fields[$name] = $definition;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getFieldNames(): array
    {
        return array_keys($this->fields);
    }

    public function getField(string $name): FieldDefinition
    {
        if (!array_key_exists($name, $this->fields)) {
            throw new \Exception('Unable to find field by name: ' . $name);
        }
        return $this->fields[$name];
    }
}
