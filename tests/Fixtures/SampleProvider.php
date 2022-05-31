<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Fixtures;

use Phodam\PhodamValueCreatorAware;
use Phodam\PhodamValueCreatorAwareTrait;
use Phodam\Provider\ProviderInterface;

class SampleProvider implements ProviderInterface, PhodamValueCreatorAware
{
    use PhodamValueCreatorAwareTrait;

    public function create(array $overrides = [], array $config = [])
    {
        $defaults = [
            'field1' => $this->phodam->create('string'),
            'field2' => $this->phodam->create('string')
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