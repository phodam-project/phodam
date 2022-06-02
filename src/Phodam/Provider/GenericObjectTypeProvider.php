<?php

// This file is part of Phodam
// Copyright (c) Andrew Vehlies <avehlies@gmail.com>
// Licensed under the MIT license. See LICENSE file in the project root.
// SPDX-License-Identifier: MIT

declare(strict_types=1);

namespace Phodam\Provider;

class GenericObjectTypeProvider implements ProviderInterface
{
    public function create(array $overrides = [], array $config = [])
    {
        /*
         * i really think that i should allow people to pass in a field config
         * so just an object that is:
         * class SportsTeam {
         *     int $founded;
         *     string $league;
         *     string $name;
         *     bool $active;
         *     array $players;
         * }
         *
         * someone could really just pass
         * [
         *     'founded' => 'int',
         *     'league' => 'string',
         *     'name' => 'string',
         *     'active' => 'bool',
         *     'players' => [
         *         'type' => array,
         *         'ref' => Player::class
         *     ]
         * ]
         *
         * and Phodam should *generate* a configuration like that when judging
         * if it can parse an object!
         *
         * i should also move $providers, $namedProviders, $arrayProviders
         * into some sort of PhodamContext class that can keep track of a list
         * of types that it knows about,
         * so if you have a SportsTeam that has like a Player associated with it
         * and then Player has a SportsTeam on it (which would cause a parser to
         * go into a circular reference), when checking SportsTeam,
         * we could basically say "ok hey i know
         * how to parse SportsTeam" and then when it gets to player it checks and
         * says "Oh SportsTeam, I know that. No need to analyze that one again."
         */


        // use reflection to read all fields
        // see if you can guess the types
        // if so, you're good
    }
}
