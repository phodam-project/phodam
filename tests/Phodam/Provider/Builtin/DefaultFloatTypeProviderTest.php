<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Phodam\Provider\Builtin;

use Phodam\PhodamInterface;
use Phodam\Provider\Primitive\DefaultFloatTypeProvider;
use Phodam\Provider\ProviderContext;
use Phodam\Tests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Phodam\Provider\Primitive\DefaultFloatTypeProvider
 */
class DefaultFloatTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultFloatTypeProvider $provider;

    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultFloatTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreate()
    {
        // defaults
        $min = -10000;
        $max = 10000;

        $context = new ProviderContext($this->phodam, 'float', [], []);

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsFloat($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreateWithConfigMinAndMax()
    {
        // defaults
        $min = -100;
        $max = 100;

        $context = new ProviderContext(
            $this->phodam,
            'float',
            [],
            ['min' => $min, 'max' => $max]
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsFloat($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }

    /**
     * @covers ::create
     * @uses \Phodam\Provider\ProviderContext
     */
    public function testCreateWithConfigMinAndMaxWithSmallRange()
    {
        // defaults
        $min = -1;
        $max = 1;

        $context = new ProviderContext(
            $this->phodam,
            'float',
            [],
            ['min' => $min, 'max' => $max]
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertIsFloat($value);
            $this->assertGreaterThanOrEqual($min, $value);
            $this->assertLessThanOrEqual($max, $value);
        }
    }
}
