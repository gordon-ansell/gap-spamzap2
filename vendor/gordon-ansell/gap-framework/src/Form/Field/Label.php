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

class Label extends Field
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
        parent::__construct($form, 'label', $params);
    }

}