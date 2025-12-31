<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Phodam\Store;

use Phodam\Store\ProviderNotFoundException;
use PhodamTests\Phodam\PhodamBaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(\Phodam\Store\ProviderNotFoundException::class)]
#[CoversMethod(\Phodam\Store\ProviderNotFoundException::class, '__construct')]
#[CoversMethod(\Phodam\Store\ProviderNotFoundException::class, 'getType')]
class ProviderNotFoundExceptionTest extends PhodamBaseTestCase
{
    public function testConstruct(): void
    {
        $message = 'My Message Here';

        $ex = new ProviderNotFoundException(
            $message
        );

        $this->assertEquals($message, $ex->getMessage());
    }
}
