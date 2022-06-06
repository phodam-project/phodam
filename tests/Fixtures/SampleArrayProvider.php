<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

use Phodam\PhodamAware;
use Phodam\PhodamAwareTrait;
use Phodam\Provider\ProviderInterface;

class SampleArrayProvider implements ProviderInterface, PhodamAware
{
    use PhodamAwareTrait;

    public function create(array $overrides = [], array $config = [])
    {
        $defaults = [
            'field1' => 'value1',
            'field2' => 'second value',
            'field3' => $this->phodam->create('int')
        ];

        return array_merge(
            $defaults, $overrides
        );
    }
}