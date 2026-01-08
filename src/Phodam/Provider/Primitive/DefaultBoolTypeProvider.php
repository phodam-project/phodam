<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace Phodam\Provider\Primitive;

use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends bool
 * @template-implements TypedProviderInterface<bool>
 */
#[PhodamProvider('bool')]
class DefaultBoolTypeProvider implements TypedProviderInterface
{
    /**
     * @inheritDoc
     * @return bool
     */
    public function create(ProviderContextInterface $context): bool
    {
        return (bool) rand(0, 1);
    }
}
