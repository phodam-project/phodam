<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex03_NamedProviders;

use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\ProviderInterface;

class UserProfileArrayProvider implements ProviderInterface
{
    /**
     * @inheritDoc
     * @return array
     */
    public function create(ProviderContextInterface $context): array
    {
        $defaults = [
            'firstName' => $context->create('string'),
            'lastName' => $context->create('string'),
            'email' => $context->create('string'),
            'age' => $context->create('int', null, [], ['min' => 18, 'max' => 100])
        ];

        // Merge with any overrides
        return array_merge($defaults, $context->getOverrides());
    }
}

