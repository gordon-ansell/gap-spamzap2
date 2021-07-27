<?php
/**
 * This file is part of the SpamZap2.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace App\Template\Extension;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use App\Domain\DoLookup;

/**
 * IP Lookup.
 */
class IPLookupExtension implements ExtensionInterface
{
    public function register(Engine $engine)
    {
        $engine->registerFunction('iplookup', [$this, 'lookupIP']);
    }

    public function lookupIP(string $ip)
    {
        $lu = new DoLookup($ip);
        $data = $lu->getData();
        if (isset($data['raw'])) {
            unset($data['raw']);
        }
        $ret = '';

        foreach ($data as $k => $v) {
            $ret .= '<div class="lookup-line">' . PHP_EOL;
                $ret .= '<span class="lookup-key">' . $k . '</span>' . PHP_EOL;
                $ret .= '<span class="lookup-value">';
                if (is_array($v)) {
                    $ret .= implode(', ', $v);
                } else {
                    $ret .= $v;
                }
                $ret .= '</span>' . PHP_EOL;
            $ret .= '</div>' . PHP_EOL;
        }

        return $ret;
    }

}
