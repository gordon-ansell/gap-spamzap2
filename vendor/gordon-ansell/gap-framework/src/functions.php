<?php
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use GreenFedora\Container\Container;
use GreenFedora\Container\ContainerInterface;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Stdlib\VarDumper;

/**
 * A bunch of things for the global context.
 */

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @return ContainerInterface
     */
    function app(): ContainerInterface
    {
        return Container::getInstance();
    }
}

if (! function_exists('logger')) {
    /**
     * Get the logger.
     *
     * @return LoggerInterface
     */
    function logger(): LoggerInterface
    {
        return Container::getInstance()->singleton('logger');
    }
}

if (!function_exists('dump')) {
    /**
     * Dump something.
     * 
     * @param   mixed   $var    Variable to dump.
     * @return  string
     */
    function dump($var): string
    {
        return VarDumper::dump($var);
    }
}

if (!function_exists('uuid_create')) {
    /**
     * Create a UUID.
     * 
     * @return  string
     */
    function uuid_create(): string
    {
        $uuid = bin2hex(random_bytes(16));

        return sprintf('%08s-%04s-4%03s-%04x-%012s',
            substr($uuid, 0, 8),    // time_low.
            substr($uuid, 8, 4),    // time_mid.
            substr($uuid, 13, 3),   // time_hi_and_version.
            hexdec(substr($uuid, 16, 4)) & 0x3fff | 0x8000, //clk_seq_hi_res, clk_seq_low and variant.
            substr($uuid, 20, 12)   // node.
        );
    }
}

