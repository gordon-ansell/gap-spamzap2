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
use GreenFedora\Form\Field\Checkbox;
use GreenFedora\Form\FormInterface;
use GreenFedora\Html\Html;

/**
 * Form field class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class CheckboxSet extends Field
{
    /**
     * Select options.
     * @var array
     */
    protected $options = [];

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
        if (array_key_exists('options', $params)) {
            $this->options = $params['options'];
            unset($params['options']);
        }
        if (array_key_exists('onclick', $params)) {
            $this->onclick = $params['onclick'];
            unset($params['onclick']);
        }
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
        $opts = '';

        $span = new Html('span', ['class' => 'label'], $this->getParam('label'));
        $opts .= $span->render() . PHP_EOL;
        
        foreach ($this->options as $k => $v) {

            $cb = new Checkbox($this->form, ['name' => $this->getName() . '_' . $k, 'value' => true, 'label' => $v], true);
            $opts .= $cb->render();


            /*
            $input = new Html('input', ['type' => 'checkbox', 'id' => $this->getName() . '_' . $k, 'value' => true, 
                'name' => $this->getName() . '_' . $k]);
            if ($this->getValue() == $k) {
                $input->setParam('checked', true);
            }
            if (null !== $this->onclick) {
                $input->setParam('onclick', $this->onclick);
            }
            $opts .= $input->render() . PHP_EOL;
            $label = new Html('label', ['for' => $this->getName() . '_' . $k], $v);
            $opts .= $label->render() . PHP_EOL;
            */
        }

        return parent::render($opts);
    }

}