<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Fixtures;

use Phodam\Provider\ProviderContext;
use Phodam\Provider\ProviderInterface;

class SampleArrayProvider implements ProviderInterface
{
    public function create(ProviderContext $context)
    {
        $defaults = [
            'field1' => 'value1',
            'field2' => 'second value',
            'field3' => $context->create('int')
        ];

        return array_merge(
            $defaults, $context->getOverrides()
        );
    }
}
