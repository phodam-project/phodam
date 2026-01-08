<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Fixtures;

use Phodam\Provider\ProviderBundleInterface;

class SampleProviderBundle implements ProviderBundleInterface
{
    public function getProviders(): array
    {
        return [];
    }

    public function getTypeDefinitions(): array
    {
        return [];
    }
}

