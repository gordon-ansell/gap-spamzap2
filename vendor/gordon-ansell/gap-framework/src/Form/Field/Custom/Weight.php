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
namespace GreenFedora\Form\Field\Custom;

use GreenFedora\Form\Field\Field;
use GreenFedora\Form\Field\Label;
use GreenFedora\Form\Field\Select;
use GreenFedora\Form\FormInterface;
use GreenFedora\Html\Html;

/**
 * Custom form field class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Weight extends Field
{
    /**
     * Label.
     */
    protected $ourLabel = null;

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
        if (array_key_exists('label', $params)) {
            $this->ourLabel = $params['label'];
            unset($params['label']);
        }
        $params['style'] = 'max-width: 6em';
        parent::__construct($form, 'input', $params, $autoLabel, $allowAutoWrap);
    }

    /**
     * Render the field.
     * 
     * @param   string  $data   data to use.
     * @return  string          Rendered form HTML.
     */
    public function render(?string $data = null): string
    {
        $fieldset = new Html('fieldset', ['name' => $this->getName() . 'fieldset', 'class' => 'duofield']);
        $label = new Label($this->form, ['for' => $this->getName()]);
        $options = array('kg' => 'kg', 'lb' => 'lb');
        $select = new Select($this->form, ['options' => $options, 'name' => $this->getName() . 'Units', 
            'style' => 'max-width: 4em'], false, false);

        $weightPlusUnits = parent::render() . $select->render();

        $span = new Html('span', ['class' => 'duowrap']);

        $ret = $label->render($this->ourLabel) . PHP_EOL . $span->render($weightPlusUnits) . PHP_EOL;

        return $fieldset->render($ret) . PHP_EOL;
    }
}