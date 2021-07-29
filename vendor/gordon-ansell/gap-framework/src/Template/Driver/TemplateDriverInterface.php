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

/**
 * Template driver interface.
 */
interface TemplateDriverInterface
{
    /**
     * Render a template.
     * 
     * @param   string      $template       Template to render.
     * @param   array       $data           Template data.
     * 
     * @return  string                      Rendered output. 
     */
    public function render(string $template, array $data = []): string;
}
