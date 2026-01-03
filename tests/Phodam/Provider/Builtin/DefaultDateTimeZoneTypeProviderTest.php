<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use DateTimeZone;
use Phodam\PhodamInterface;
use Phodam\Provider\Builtin\DefaultDateTimeZoneTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(DefaultDateTimeZoneTypeProvider::class)]
#[CoversMethod(DefaultDateTimeZoneTypeProvider::class, 'create')]
class DefaultDateTimeZoneTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultDateTimeZoneTypeProvider $provider;
    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultDateTimeZoneTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    public function testCreate()
    {
        $context = new ProviderContext(
            $this->phodam,
            'DateTimeZone',
            [],
            []
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertInstanceOf(DateTimeZone::class, $value);
        }
    }
}
