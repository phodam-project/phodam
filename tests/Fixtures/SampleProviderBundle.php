<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

use Phodam\PhodamSchemaInterface;
use Phodam\Provider\ProviderBundleInterface;

class SampleProviderBundle implements ProviderBundleInterface
{
    public function register(PhodamSchemaInterface $schema): void
    {
        // This is a test fixture, so we don't need to register anything
        // The test will verify that this method was called
    }
}

