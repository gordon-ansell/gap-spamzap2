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

use GreenFedora\Uri\Uri;
use GreenFedora\Stdlib\Exception\InvalidArgumentException;

/**
 * Domain helper.
 */
class Domain
{
    /**
     * Two part domain endings.
     * @var array
     */
    protected static $twoPart = [
        '.co.uk', 
        '.com.au', 
        '.com.cn',
        '.me.uk',
        '.org.au',
        '.org.cn',
        '.org.uk',
        '.us.org',
        '.us.com',
    ];

    /**
     * Likely two parters. Last but one segment.
     * @var array
     */
    protected static $likelyTwoPart = [
        'co',
        'com',
        'me',
        'net',
        'org',
        'us',
    ];

    /**
     * Default extraction regex.
     * @var string
     */
    protected static $defExRegex = "~https?:\/\/([a-zA-Z0-9\.\-]+)~i";

    /**
     * Extract the raw domain from a possibly subdomained string or email string.
     * 
     * @param   string      $full       Full domain.
     * @param   bool        $remwww     Remove 'www.'?
     * @return  string
     */
    public static function extractRawDomain(string $full, bool $remwww = true): string
    {
        if (false !== strpos($full, '@')) {
            $sp = explode('@', $full);
            $full = $sp[1];
        }

        $isTwoPart = false;
        foreach (self::$twoPart as $item) {
            if (str_ends_with($full, $item)) {
                $isTwoPart = true;
                break;
            }
        }

        $sp = explode('.', $full);


        if ($remwww and 'www' == $sp[0]) {
            unset($sp[0]);
            $sp = array_values($sp);
        }

        if (!$isTwoPart and count($sp) > 2) {
            foreach(self::$likelyTwoPart as $item) {
                if ($item == $sp[count($sp) - 2]) {
                    $isTwoPart = true;
                    break;
                }
            }
        }

        if ($isTwoPart and count($sp) > 2) {
            return $sp[count($sp) - 3] . '.' . $sp[count($sp) - 2] . '.' . $sp[count($sp) - 1];
        } else if (count($sp) > 1) {
            return $sp[count($sp) - 2] . '.' . $sp[count($sp) - 1];
        } else {
            return $full;
        }
    }

    /**
     * Extract the domains from a string.
     * 
     * @param   string      $input      Input string.
     * @param   string|null $regex      Regulat expression.
     * @param   bool        $remwww     Remove 'www.'?
     * @return  string[]
     */
    public static function domainsFromString(string $input, ?string $regex = null, bool $remwww = true): array
    {
        if (is_null($regex)) {
            $regex = self::$defExRegex;
        }

        $reres = preg_match_all($regex, $input, $matches);

        if (false === $reres) {
            throw new InvalidArgumentException(sprintf("Regex failure extracting domain (regex: %s).", $regex));
        }

        $ret = [];

        if (count($matches[1]) > 0) {
            foreach ($matches[1] as $thing) {
                $u = new Uri('http://' . $thing);
                $ret[] = self::extractRawDomain($u->getDomain());
            }
        }

        return $ret;
    }
}
