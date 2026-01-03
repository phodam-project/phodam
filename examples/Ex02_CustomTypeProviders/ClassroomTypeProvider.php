<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace PhodamExamples\Ex02_CustomTypeProviders;

use Phodam\PhodamAware;
use Phodam\PhodamAwareTrait;
use Phodam\Provider\PhodamProvider;
use Phodam\Provider\ProviderContextInterface;
use Phodam\Provider\TypedProviderInterface;

/**
 * @template T extends Classroom
 * @template-implements TypedProviderInterface<Classroom>
 */
#[PhodamProvider(Classroom::class)]
class ClassroomTypeProvider implements TypedProviderInterface
{

    /**
     * @inheritDoc
     * @return Classroom
     */
    public function create(ProviderContextInterface $context): Classroom
    {
        $defaults = [
            'roomNumber' => $context->getPhodam()->create(
                'int',
                null,
                [],
                ['min' => 100, 'max' => 499]
            )
        ];

        $config = $context->getConfig();
        $numStudents =
            $config['numStudents'] ??
            $context->getPhodam()->create('int', null, [], ['min' => 10, 'max' => 15]);

        $values = array_merge(
            $defaults,
            $context->getOverrides()
        );

        $classroom = new Classroom();
        $classroom->setRoomNumber((int) $values['roomNumber']);

        // Since PHP doesn't support giving a generic type in an array,
        // we need to make a custom provider to populate an array
        $students = array_map(
            fn ($i) => $context->getPhodam()->create(Student::class),
            range(0, $numStudents)
        );
        $classroom->setStudents($students);

        return $classroom;
    }
}
