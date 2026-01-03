<?php

// This file is part of Phodam
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

use Attribute;

/**
 * Attribute to declare a type provider.
 *
 * Place this attribute on a provider class to declare:
 * - The type it provides for
 * - Optional name (for named providers)
 * - Optional override flag
 */
#[Attribute(Attribute::TARGET_CLASS)]
class PhodamProvider
{
    public function __construct(
        public string $type,
        public ?string $name = null,
        public bool $overriding = false
    ) {
    }
}
