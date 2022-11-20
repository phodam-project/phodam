<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

use Phodam\Provider\ProviderContext;
use Phodam\Provider\ProviderInterface;

class SampleProvider implements ProviderInterface
{
    public function create(ProviderContext $context)
    {
        $defaults = [
            'field1' => $context->create('string'),
            'field2' => $context->create('string'),
            'field3' => $context->create('int')
        ];
        $values = array_merge(
            $defaults,
            $context->getOverrides()
        );

        $config = $context->getConfig();
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
