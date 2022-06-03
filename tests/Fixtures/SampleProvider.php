<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Tests\Fixtures;

use Phodam\PhodamAware;
use Phodam\PhodamAwareTrait;
use Phodam\Provider\ProviderInterface;

class SampleProvider implements ProviderInterface, PhodamAware
{
    use PhodamAwareTrait;

    public function create(array $overrides = [], array $config = [])
    {
        $defaults = [
            'field1' => $this->phodam->create('string'),
            'field2' => $this->phodam->create('string'),
            'field3' => $this->phodam->create('int')
        ];
        $values = array_merge(
            $defaults,
            $overrides
        );

        $minYear = PHP_INT_MIN;
        $maxYear = PHP_INT_MAX;
        if(isset($config['minYear'])) {
            $minYear = $config['minYear'];
        }
        if(isset($config['maxYear'])) {
            $maxYear = $config['maxYear'];
        }

        if ($minYear !== PHP_INT_MIN || $maxYear !== PHP_INT_MAX) {
            $values['field3'] = rand($minYear, $maxYear);
        }

        return new UnregisteredClassType(
            $values['field1'],
            $values['field2'],
            $values['field3']
        );
    }
}