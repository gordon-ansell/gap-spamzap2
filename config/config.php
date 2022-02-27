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
// PROD
// ===================

declare(strict_types=1);

use GreenFedora\Stdlib\Level;

return [
    'logger' => [
        'level' => Level::ERROR
    ],
    'db' => [
        'platform'  =>  'sqlite',
        'path'      =>  dirname(dirname(dirname(__DIR__))) . '/spamzapdb/spamzap2.db',
        'auto'      =>  true,
        //'cache'     =>  dirname(__DIR__) . '/dbstore/cache',    
    ],
    'plugin' => [
        'slug'          => 'spamzap2',
        'title'         => 'SpamZap 2',
        'prefix'        => 'sz-',
        'priority'      => 9,
        'usedefip'      => false,
        'updatepath'    => 'https://api.github.com/repos/gordon-ansell/gap-spamzap2/releases',
        'version'       => '1.0.0.dev.43'
    ],
    'template' => [
        'driver'    => 'plates',
        'path'      =>  dirname(__DIR__) . '/templates',
    ],
    'session' => array(
        'cookie_lifetime'   =>  '432000',
        'cookie_path'       =>  '/',
        'gc_maxlifetime'    =>  '432000',
        'gc_probability'    =>  '1',
        'gc_divisor'        =>  '100',
        'prefix'            =>  'ga',
        'save_path'			=>	'',
    ),
];