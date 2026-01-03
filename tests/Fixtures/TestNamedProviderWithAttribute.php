<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Copyright (c) Chris Bouchard <chris@upliftinglemma.net>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamTests\Fixtures;

use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;

#[PhodamProvider(UnregisteredClassType::class, name: 'customProvider')]
class TestNamedProviderWithAttribute implements ProviderInterface
{
    public function create(ProviderContextInterface $context)
    {
        $defaults = [
            'field1' => $context->getPhodam()->create('string'),
            'field2' => $context->getPhodam()->create('string'),
            'field3' => $context->getPhodam()->create('int')
        ];
        $values = array_merge(
            $defaults,
            $context->getOverrides()
        );

        return new UnregisteredClassType(
            $values['field1'],
            $values['field2'],
            $values['field3']
        );
    }
}
