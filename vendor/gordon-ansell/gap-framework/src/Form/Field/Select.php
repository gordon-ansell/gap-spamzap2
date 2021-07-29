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
use GreenFedora\Html\Html;

/**
 * Form field class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Select extends Field
{
    /**
     * Select options.
     * @var array
     */
    protected $options = [];

    /**
     * Inner wrap.
     * @var array|null
     */
    protected $innerWrap = array('span', array('class' => 'select-wrapper'));

    /**
     * Constructor.
     * 
     * @param   FormInterface       $form           Parent form.
     * @param   array               $params         Parameters.
     * @param   bool                $autoLabel      Autolabel?
     * @param   bool                $allowAutoWrap  Allow auto wrapping?
     * @return  void
     */
    public function __construct(FormInterface $form, array $params = [], bool $autoLabel = true, bool $allowAutoWrap = true)
    {
        if (array_key_exists('options', $params)) {
            $this->options = $params['options'];
            unset($params['options']);
        }
        if (isset($params['class']) and false !== strpos($params['class'], 'nowrap')) {
            $this->innerWrap = null;
        }
        parent::__construct($form, 'select', $params, $autoLabel, $allowAutoWrap);
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
        foreach ($this->options as $k => $v) {
            $h = new Html('option', ['value' => $k], $v);
            if ($this->getValue() == $k) {
                $h->setParam('selected', true);
            }
            $opts .= $h->render() . PHP_EOL;
        }

        return parent::render($opts);
    }

}