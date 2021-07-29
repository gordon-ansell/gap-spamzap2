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
namespace GreenFedora\Template;

use GreenFedora\Stdlib\Arr\Arr;
use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Template\Driver\TemplateDriverInterface;
use GreenFedora\Template\TemplateInterface;

/**
 * Template handler class. Acts as a wrapper for template packages.
 */
class Template implements TemplateInterface
{
    /**
     * Driver.
     * @var TemplateDriverInterface
     */
    protected $driver = null;

    /**
     * Configs.
     * @var ArrInterface
     */
    protected $cfg = null;

    /**
     * Template data.
     * @var ArrInterface
     */
    protected $data = null;

    /**
     * Constructor.
     * 
     * @param   TemplateDriverInterface     $driver     Driver for underlying template engine.
     * @param   ArrInterface                $cfg        Config.
     * 
     * @return  void
     * #[Inject (config: cfg|template)]
     */
    public function __construct(TemplateDriverInterface $driver, ArrInterface $cfg = null)
    {
        $this->driver = $driver;
        $this->cfg = $cfg;

        $this->data = new Arr();
    }

    /**
     * Render a template.
     * 
     * @param   string      $template       Template to render.
     * @param   array|null  $data           Template data or null to use class data.
     * 
     * @return  string                      Rendered output. 
     */
    public function render(string $template, ?array $data = null): string
    {
        if (is_null($data)) {
            $data = $this->data->toArray();
        }
        return $this->driver->render($template, $data);
    }

    /**
     * Get the data.
     * 
     * @return  ArrInterface
     */
    public function getData(): ArrInterface
    {
        return $this->data;
    }
}
