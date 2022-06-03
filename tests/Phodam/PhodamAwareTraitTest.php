<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam;

use Phodam\Phodam;
use Phodam\Tests\Fixtures\SampleProvider;

/**
 * @coversDefaultClass \Phodam\PhodamAwareTrait
 */
class PhodamAwareTraitTest extends PhodamBaseTestCase
{
    private Phodam $phodam;

    public function setUp(): void
    {
        parent::setUp();
        $this->phodam = new Phodam();
    }

    /**
     * @covers ::setPhodam
     */
    public function testSetPhodam(): void
    {
        $provider = new SampleProvider();
        $provider->setPhodam($this->phodam);

        $this->assertInstanceOf(Phodam::class, $this->phodam);
    }
}