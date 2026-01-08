<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace Phodam\Provider\Builtin;

use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;
use ReflectionClass;
use ReflectionException;

/**
 * A generic provider for PHP 8 enums that returns a random enum case.
 * This provider works with any enum type (both pure enums and backed enums).
 */
class DefaultEnumTypeProvider implements ProviderInterface
{
    /**
     * @inheritDoc
     * @return \UnitEnum Returns a random case from the enum
     * @throws ReflectionException if the enum class cannot be reflected
     */
    public function create(ProviderContextInterface $context)
    {
        $type = $context->getType();

        try {
            $reflection = new ReflectionClass($type);
        } catch (ReflectionException $e) {
            throw new \InvalidArgumentException(
                "Type {$type} cannot be reflected: " . $e->getMessage()
            );
        }

        if (!$reflection->isEnum()) {
            throw new \InvalidArgumentException(
                "Type {$type} is not an enum"
            );
        }

        // Get all enum cases using the enum's cases() method
        // This works for both pure enums (UnitEnum) and backed enums (BackedEnum)
        $cases = $type::cases();

        if (empty($cases)) {
            throw new \RuntimeException(
                "Enum {$type} has no cases"
            );
        }

        // Return a random enum case
        return $cases[array_rand($cases)];
    }
}
