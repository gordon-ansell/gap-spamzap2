<?php
/**
 * This file is part of the SpamZap2 package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// ===================
// DEV
// ===================

declare(strict_types=1);

use GreenFedora\Stdlib\Level;

return [
    'logger' => [
        'level' => Level::DEBUG,
        'sections'  => ['DB:CACHE']
    ],
    'plugin' => [
        'defaultip' => '217.155.193.33',
        'usedefip'  => true,
    ],
];