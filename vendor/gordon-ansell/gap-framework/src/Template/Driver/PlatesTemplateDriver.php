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
namespace GreenFedora\Template\Driver;

use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Template\Driver\AbstractTemplateDriver;
use GreenFedora\Template\Driver\TemplateDriverInterface;

/**
 * Template driver for league/plates.
 */
class PlatesTemplateDriver extends AbstractTemplateDriver implements TemplateDriverInterface
{
    /**
     * The template emgine.
     * @var \League\Plates\Engine
     */
    protected $engine = null;

    /**
     * Constructor.
     * 
     * @param   ArrInterface                $cfg        Config.
     * 
     * @return  void
     * #[Inject (config: cfg|template)]
     */
    public function __construct(ArrInterface $cfg = null)
    {
        parent::__construct($cfg);

        $this->engine = new \League\Plates\Engine($this->cfg->get('path'));
        $this->loadExtensions();
    }

    /**
     * Render a template.
     * 
     * @param   string      $template       Template to render.
     * @param   array       $data           Template data.
     * 
     * @return  string                      Rendered output. 
     */
    public function render(string $template, array $data = []): string
    {
        return $this->engine->render($template, $data);
    }
}
