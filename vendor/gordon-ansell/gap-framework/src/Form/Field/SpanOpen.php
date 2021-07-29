<?php

/**
 * This file is part of the GordyAnsell GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Form\Field;

use GreenFedora\Form\Field\Field;
use GreenFedora\Form\FormInterface;

/**
 * Form field class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class SpanOpen extends Field
{
    /**
     * Constructor.
     * 
     * @param   FormInterface       $form       Parent form.
     * @param   array               $params     Parameters.
     * @return  void
     */
    public function __construct(FormInterface $form, array $params = [])
    {
        parent::__construct($form, 'span', $params);
    }

    /**
     * Render the field.
     * 
     * @param   string      $data           Data to use.
     * @param   array       $extraParams    Extra params for this render.
     * @return  string                      Rendered form HTML.
     */
    public function render(?string $data = null, ?array $extraParams = null): string
    {
        return parent::renderOpen() . PHP_EOL;
    }
}