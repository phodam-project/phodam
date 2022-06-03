<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use Phodam\PhodamAware;
use Phodam\PhodamAwareTrait;
use ReflectionClass;

/**
 * @template T
 */
class DefinitionBasedTypeProvider implements ProviderInterface, PhodamAware
{
    use PhodamAwareTrait;

    private string $type;
    /** @var array<string, array<string, mixed>> */
    private array $definition;

    /**
     * @param class-string<T> $type
     * @param array<string, array<string, mixed>> $definition
     */
    public function __construct(
        string $type, array $definition
    ) {
        $this->type = $type;
        $this->definition = $definition;
    }

    /**
     * @param array<string, mixed> $overrides
     * @param array<string, mixed> $config
     * @return mixed
     * @throws \ReflectionException
     */
    public function create(array $overrides = [], array $config = [])
    {
        $refClass = new ReflectionClass($this->type);
        $obj = $refClass->newInstanceWithoutConstructor();

        foreach ($this->definition as $fieldName => $def) {
            $refProperty = $refClass->getProperty($fieldName);
            if (array_key_exists($fieldName, $overrides)) {
                $val = $overrides[$fieldName];
            } else {
                $val = $this->phodam->create(
                    $def['type'],
                    $def['name'] ?? null,
                    $def['overrides'] ?? [],
                    $def['config'] ?? []
                );
            }
            $refProperty->setAccessible(true);
            $refProperty->setValue($obj, $val);
        }

        return $obj;
    }
}
