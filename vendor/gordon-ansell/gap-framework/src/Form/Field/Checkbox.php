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

use GreenFedora\Form\Field\Input;
use GreenFedora\Form\FormInterface;
use GreenFedora\Html\Html;

/**
 * Form field class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Checkbox extends Field
{
    /**
     * Onclick setting.
     * @var string|null
     */
    protected $onclick = null;

    /**
     * Constructor.
     * 
     * @param   FormInterface       $form           Parent form.
     * @param   array               $params         Parameters.
     * @param   bool                $autoLabel      Autolabel?
     * @param   bool                $allowAutoWrap  Allow auto wrapping?
     * @return  void
     */
    public function __construct(FormInterface $form, array $params = [], bool $autoLabel = false, bool $allowAutoWrap = false)
    {
        /*
        if (array_key_exists('onclick', $params)) {
            $this->onclick = $params['onclick'];
            unset($params['onclick']);
        }
        $params['type'] = 'checkbox';
        if (!isset($params['value'])) {
            $params['value'] = $params['name'];
        }
        */
        parent::__construct($form, 'fieldset', $params, $autoLabel, $allowAutoWrap);
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
        $html= '';
        
        $input = new Html('input', ['blah' => '123', 'type' => 'checkbox', 
            'id' => $this->getName(), 'name' => $this->getName()], null, null, false);
 
        if ("on" == $this->getValue()) {
            $input->setParam('checked', "checked");
        }

        $html .= $input->render();

        $label = new Html('label', ['for' => $this->getName()], $this->getParam('label'));
        $this->after = $label->render() . PHP_EOL;

        return parent::render($html);
    }

}