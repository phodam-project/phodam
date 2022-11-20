<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Provider\Builtin;

use DateTime;
use Phodam\PhodamInterface;
use Phodam\Provider\Builtin\DefaultDateTimeTypeProvider;
use Phodam\Provider\ProviderContext;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Phodam\Provider\Builtin\DefaultDateTimeTypeProvider
 */
class DefaultDateTimeTypeProviderTest extends PhodamBaseTestCase
{
    private DefaultDateTimeTypeProvider $provider;
    /** @var PhodamInterface & MockObject */
    private $phodam;

    public function setUp(): void
    {
        $this->provider = new DefaultDateTimeTypeProvider();
        $this->phodam = $this->createMock(PhodamInterface::class);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $context = new ProviderContext(
            $this->phodam,
            'DateTime',
            [],
            []
        );

        for ($i = 0; $i < 10; $i++) {
            $value = $this->provider->create($context);
            $this->assertInstanceOf(DateTime::class, $value);
        }
    }
}
