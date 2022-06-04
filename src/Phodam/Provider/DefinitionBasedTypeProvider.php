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
use ReflectionProperty;

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
        string $type,
        array $definition
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
        // okay, so here's some thoughts.
        // a definition shouldn't have to be complete, we should only HAVE
        //     to define the fields that the type analyzer can't handle
        // not sure the order on how i want to do this, but...
        // 1. get a list of class fields
        // 2. get a list of definition fields
        // 3. check to see which fields don't overlap
        // 4. if the definition handles it all, then that's fine
        // 5. if it doesn't, start up a type analyzer
        // 6. generate a definition for the type analyzer, if it completes
        //    then fill in the remaining fields with the results
        // 7. if it throws an exception, check the $mappedFields from
        //    the exception
        // 8. if $mappedFields covers the difference in fields, then you're good
        // 9. if it doesn't, then throw an exception and give up

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
