<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex03_NamedProviders;

use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends User
 * @template-implements TypedProviderInterface<User>
 */
#[PhodamProvider(User::class, name: 'inactive')]
class InactiveUserProvider implements TypedProviderInterface
{
    /**
     * @inheritDoc
     * @return User
     */
    public function create(ProviderContextInterface $context): User
    {
        $defaults = [
            'name' => $context->getPhodam()->create('string'),
            'email' => $context->getPhodam()->create('string'),
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

