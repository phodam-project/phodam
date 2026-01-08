<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

namespace PhodamExamples\Ex03_NamedProviders;

use Phodam\Provider\PhodamArrayProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;

#[PhodamArrayProvider('userProfile')]
class UserProfileArrayProvider implements ProviderInterface
{
    /**
     * @inheritDoc
     * @return array
     */
    public function create(ProviderContextInterface $context): array
    {
        $defaults = [
            'firstName' => $context->getPhodam()->create('string'),
            'lastName' => $context->getPhodam()->create('string'),
            'email' => $context->getPhodam()->create('string'),
            'age' => $context->getPhodam()->create('int', config: ['min' => 18, 'max' => 100])
        ];

        // Merge with any overrides
        return array_merge($defaults, $context->getOverrides());
    }
}

