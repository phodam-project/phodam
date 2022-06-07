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
 * @template T extends Classroom
 * @template-implements TypedProviderInterface<Classroom>
 */
class ClassroomTypeProvider implements TypedProviderInterface, PhodamAware
{
    use PhodamAwareTrait;

    /**
     * @inheritDoc
     * @return Classroom
     */
    public function create(array $overrides = [], array $config = []): Classroom
    {
        $defaults = [
            'roomNumber' => $this->phodam->create(
                'int',
                null,
                [],
                [ 'min' => 100, 'max' => 499 ]
            )
        ];

        $numStudents =
            $config['numStudents'] ??
            $this->phodam->create('int', null, [], [ 'min' => 10, 'max' => 15]);

        $values = array_merge(
            $defaults,
            $overrides
        );

        $classroom = new Classroom();
        $classroom->setRoomNumber($values['roomNumber']);

        // Since PHP doesn't support giving a generic type in an array,
        // we need to make a custom provider to populate an array
        $students = array_map(
            function ($index) {
                $gpa = $this->phodam->create(
                    'float',
                    null,
                    [],
                    [ 'min' => 0.0, 'max' => 4.0, 'precision' => 2 ]
                );
                return $this->phodam->create(
                    Student::class,
                    null,
                    [
                        'gpa' => $gpa,
                        'active' => true
                    ]
                );
            },
            range(0, $numStudents)
        );
        $classroom->setStudents($students);

        return $classroom;
    }
}
