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
 * Base plugin class.
 */
abstract class PluginBase
{
    /**
     * Application.
     * @var WordpressApplicationInterface
     */
    protected $app = null;

    /**
     * Configs.
     * @var ArrInterface
     */
    protected $cfg = null;

    /**
     * Constructor.
     * 
     * @param    WordpressApplicationInterface  $app    Application.
     * 
     * @return   void
     */
    public function __construct(WordpressApplicationInterface $app)
    {
        $this->app = $app;
        $this->cfg = $this->app->getConfig('plugin');
    }

    /**
     * Get the application.
     * 
     * @return WordpressApplicationInterface
     */
    public function getApp(): WordpressApplicationInterface
    {
        return $this->app;
    }

    /**
     * Get the configs.
     * 
     * @return  ArrInterface
     */
    public function getCfg(): ArrInterface
    {
        return $this->cfg;
    }

    /**
 	 * Return a prefixed string.
 	 *
	 * @param 	string 	$str 	String to prefix.
     *
	 * @return 	string 			Prefixed string. 
	 */
	public function pref($str): string
	{
		return $this->cfg->get('prefix') . $str;
	}

	/**
	 * Return a short prefixed string.
	 *
	 * @param 	string 	$str 	String to prefix.
     *    
	 * @return 	string 			Prefixed string. 
	 */
	public function spref($str): string
	{
		return rtrim($this->cfg->get('prefix'), '-') . $str;
	}

    /**
	 * Text string, for current slug domain.
	 *
	 * @param 	string 	$txt 	Text.
	 * @return 	string 			Properly slugified text. 
	 */
	public function __($txt): string
	{
		return \__($txt, $this->cfg->get('slug'));
	}

    /**
     * Get the datetime.
     * 
     * @param
     * @return  string
     */
    public function getDt(): string
    {
        $dt = new \DateTime("now", new \DateTimeZone('UTC'));
        return $dt->format(\DateTimeInterface::ATOM);        
    }
}
