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
namespace GreenFedora\Wordpress;

use GreenFedora\Wordpress\WordpressApplicationInterface;
use GreenFedora\Stdlib\Arr\ArrInterface;

/**
 * Base plugin interface.
 */
interface PluginBaseInterface
{
    /**
     * Get the application.
     * 
     * @return WordpressApplicationInterface
     */
    public function getApp(): WordpressApplicationInterface;

    /**
     * Get the configs.
     * 
     * @return  ArrInterface
     */
    public function getCfg(): ArrInterface;

    /**
 	 * Return a prefixed string.
 	 *
	 * @param 	string 	$str 	String to prefix.
     *
	 * @return 	string 			Prefixed string. 
	 */
	public function pref($str): string;

	/**
	 * Return a short prefixed string.
	 *
	 * @param 	string 	$str 	String to prefix.
     *    
	 * @return 	string 			Prefixed string. 
	 */
	public function spref($str): string;

    /**
	 * Text string, for current slug domain.
	 *
	 * @param 	string 	$txt 	Text.
	 * @return 	string 			Properly slugified text. 
	 */
	public function __($txt): string;
}
