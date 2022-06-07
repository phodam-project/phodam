<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex02_CustomTypeProviders;

use Phodam\PhodamAware;
use Phodam\PhodamAwareTrait;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends Student
 * @template-implements TypedProviderInterface<Student>
 */
class StudentTypeProvider implements TypedProviderInterface, PhodamAware
{
    use PhodamAwareTrait;

    private int $id = 1;

    /**
     * @inheritDoc
     * @return Student
     */
    public function create(array $overrides = [], array $config = []): Student
    {
        $defaults = [
            'id' => $this->id++,
            'name' => $this->phodam->create('string'),
            'gpa' => $this->phodam->create(
                'float',
                null,
                [],
                [ 'min' => 0.0, 'max' => 4.0, 'precision' => 2 ]
            ),
            'active' => true,
            'address' => $this->phodam->create(Address::class)
        ];

        $values = array_merge(
            $defaults,
            $overrides
        );

        return (new Student())
            ->setId($values['id'])
            ->setName($values['name'])
            ->setGpa($values['gpa'])
            ->setActive($values['active'])
            ->setAddress($values['address']);
    }
}
