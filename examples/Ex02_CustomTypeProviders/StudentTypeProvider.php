<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex02_CustomTypeProviders;

use DateTimeImmutable;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends Student
 * @template-implements TypedProviderInterface<Student>
 */
class StudentTypeProvider implements TypedProviderInterface
{
    private int $id = 1;

    /**
     * @inheritDoc
     * @return Student
     */
    public function create(ProviderContextInterface $context): Student
    {
        $defaults = [
            'id' => $this->id++,
            'name' => $context->create('string'),
            'gpa' => $context->create(
                'float',
                null,
                [],
                ['min' => 0.0, 'max' => 4.0, 'precision' => 2]
            ),
            'active' => true,
            'address' => $context->create(Address::class),
            'dateOfBirth' => $context->create(DateTimeImmutable::class)
        ];

        $values = array_merge(
            $defaults,
            $context->getOverrides()
        );

        return (new Student())
            ->setId((int) $values['id'])
            ->setName($values['name'])
            ->setGpa((float) $values['gpa'])
            ->setActive((bool) $values['active'])
            ->setAddress($values['address'])
            ->setDateOfBirth($values['dateOfBirth']);
    }
}
