<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Store;

use Phodam\Store\ProviderNotFoundException;
use PhodamTests\Fixtures\SimpleType;
use PhodamTests\Phodam\PhodamBaseTestCase;

/**
 * @coversDefaultClass \Phodam\Store\ProviderNotFoundException
 */
class ProviderNotFoundExceptionTest extends PhodamBaseTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getType
     */
    public function testConstruct(): void
    {
        $message = 'My Message Here';

        $ex = new ProviderNotFoundException(
            $message
        );

        $this->assertEquals($message, $ex->getMessage());
    }
}