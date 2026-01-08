<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Fixtures;

use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;

#[PhodamProvider('string', overriding: true)]
class TestProviderWithOverridingAttribute implements ProviderInterface
{
    public function create(ProviderContextInterface $context)
    {
        return 'custom value';
    }
}
