<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use DatePeriod;
use Phodam\PhodamInterface;
use Phodam\Provider\Builtin\DefaultDatePeriodTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(DefaultDatePeriodTypeProvider::class)]
#[CoversMethod(DefaultDatePeriodTypeProvider::class, 'create')]
class DefaultDatePeriodTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultDatePeriodTypeProvider $provider;
    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultDatePeriodTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    public function testCreate()
    {
        $context = new ProviderContext(
            $this->phodam,
            'DatePeriod',
            [],
            []
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertInstanceOf(DatePeriod::class, $value);
        }
    }
}
