<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT



namespace PhodamTests\Fixtures;

use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;

class SampleArrayProvider implements ProviderInterface
{
    public function create(ProviderContextInterface $context)
    {
        $defaults = [
            'field1' => 'value1',
            'field2' => 'second value',
            'field3' => $context->getPhodam()->create('int')
        ];

        return array_merge(
            $defaults,
            $context->getOverrides()
        );
    }
}
