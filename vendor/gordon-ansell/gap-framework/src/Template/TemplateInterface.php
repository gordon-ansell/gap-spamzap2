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

use GreenFedora\Stdlib\Arr\ArrInterface;

/**
 * Interface for the Template class.
 */
interface TemplateInterface
{
    /**
     * Render a template.
     * 
     * @param   string      $template       Template to render.
     * @param   array|null  $data           Template data or null to use class data.
     * 
     * @return  string                      Rendered output. 
     */
    public function render(string $template, ?array $data = null): string;

    /**
     * Get the data.
     * 
     * @return  ArrInterface
     */
    public function getData(): ArrInterface;
}
