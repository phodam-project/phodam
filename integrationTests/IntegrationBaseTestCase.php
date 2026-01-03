<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Integration;

use Phodam\PhodamInterface;
use Phodam\PhodamSchema;
use PHPUnit\Framework\TestCase;

abstract class IntegrationBaseTestCase extends TestCase
{
    protected function createPhodamWithDefaults(): PhodamInterface
    {
        $schema = PhodamSchema::withDefaults();
        return $schema->getPhodam();
    }

    protected function createBlankPhodam(): PhodamInterface
    {
        $schema = PhodamSchema::blank();
        return $schema->getPhodam();
    }
}
