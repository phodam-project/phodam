<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex03_NamedProviders;

use Phodam\Provider\ProviderContext;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends User
 * @template-implements TypedProviderInterface<User>
 */
class InactiveUserProvider implements TypedProviderInterface
{
    /**
     * @inheritDoc
     * @return User
     */
    public function create(ProviderContext $context): User
    {
        $defaults = [
            'name' => $context->create('string'),
            'email' => $context->create('string'),
            'active' => false  // Always inactive
        ];

        $values = array_merge(
            $defaults,
            $context->getOverrides()
        );

        return (new User())
            ->setName($values['name'])
            ->setEmail($values['email'])
            ->setActive((bool) $values['active']);
    }
}

