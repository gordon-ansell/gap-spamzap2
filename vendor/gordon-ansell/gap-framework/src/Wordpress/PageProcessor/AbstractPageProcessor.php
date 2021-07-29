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
 * Page processor.
 */
abstract class AbstractPageProcessor
{
    /**
     * Parent.
     * @var PluginAdminInterface
     */
    protected $parent = null;

    /**
     * Constructor.
     * 
     * @param   PluginAdminInterface  $parent     Parent class.
     * 
     * @return  void
     */
    public function __construct(PluginAdminInterface $parent)
    {
        $this->parent = $parent;
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
		return $this->parent->getCfg()->get('prefix') . $str;
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
		return rtrim($this->parent->getCfg()('prefix'), '-') . $str;
	}

    /**
	 * Text string, for current slug domain.
	 *
	 * @param 	string 	$txt 	Text.
	 * @return 	string 			Properly slugified text. 
	 */
	public function __($txt): string
	{
		return \__($txt, $this->parent->getCfg()('slug'));
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

    /**
     * Convert a date/time.
     * 
     * @param   string      $dt         Date/time to convert.
     * @return  string                  Converted date/time.
     */
    public function convDt(string $dt): string
    {
        $conv = new \DateTime($dt, new \DateTimeZone('UTC'));
        $tz = \get_option('timezone_string');
        $conv->setTimezone(new \DateTimeZone($tz));
        return $conv->format("Y-m-d H:i:s");        
    }

    /**
     * Process the page.
     * 
     * @return
     */
    abstract public function process();
}