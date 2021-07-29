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
namespace GreenFedora\Wordpress\PageProcessor;

use GreenFedora\Wordpress\PluginAdminInterface;

/**
 * Page processor interface.
 */
interface PageProcessorInterface
{
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

    /**
     * Get the datetime.
     * 
     * @param
     * @return  string
     */
    public function getDt(): string;

    /**
     * Process the page.
     * 
     * @return
     */
    public function process();
}