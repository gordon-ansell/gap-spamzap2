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
namespace GreenFedora\Stdlib;

/**
 * Message levels,
 * @link http://seldaek.github.io/monolog/
 */
class Level
{
    /**
     * Level constants. They match Monolog, for what it's worth.
     */
    public const DEBUG = 100;
    public const INFO = 200;
    public const NOTICE = 250;
    public const WARNING = 300;
    public const ERROR = 400;
    public const CRITICAL = 500;
    public const ALERT = 550;
    public const EMERGENCY = 600;

    /**
     * Message tags.
     */
    const TAG_DEBUG     =   'debug';
    const TAG_INFO      =   'info';
    const TAG_NOTICE    =   'notice';
    const TAG_WARNING   =   'warning';
    const TAG_ERROR     =   'error';
    const TAG_CRITICAL  =   'critical';
    const TAG_ALERT     =   'alert';
    const TAG_EMERGENCY =   'emergency';

    /**
     * Levels to tags.
     * @var array
     */
    const L2T = [
        self::DEBUG     =>  self::TAG_DEBUG,
        self::INFO      =>  self::TAG_INFO,
        self::NOTICE    =>  self::TAG_NOTICE,
        self::WARNING   =>  self::TAG_WARNING,
        self::ERROR     =>  self::TAG_ERROR,
        self::CRITICAL  =>  self::TAG_CRITICAL,
        self::ALERT     =>  self::TAG_ALERT,
        self::EMERGENCY =>  self::TAG_EMERGENCY,
    ];

    /**
     * Convert a level to a tag.
     * 
     * @param   int         $level      Level.
     * @return  string|null
     */
    public static function l2t(int $level): ?string
    {
        if (array_key_exists($level, self::L2T)) {
            return self::L2T[$level];
        }
        return null;
    }

    /**
     * Convert a tag to a level.
     * 
     * @param   string         $tag      Tag.
     * @return  int|null
     */
    public static function t2l(string $level): ?int
    {
        $r = array_search($level, self::L2T);
        if (false === $r) {
            return null;
        }
        return $r;
    }
}