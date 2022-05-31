<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Fixtures;

use Phodam\Provider\ProviderInterface;

class SampleProvider implements ProviderInterface
{
    public function create(array $overrides = [], array $config = [])
    {
        $defaults = [
            'field1' => 'value1',
            'field2' => 'second value'
        ];
        $values = array_merge(
            $defaults,
            $overrides
        );
        return new UnregisteredClassType(
            $values['field1'],
            $values['field2']
        );
    }
}