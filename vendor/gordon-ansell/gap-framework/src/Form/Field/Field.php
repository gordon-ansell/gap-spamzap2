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

use GreenFedora\Html\Html;
use GreenFedora\Form\Field\FieldInterface;
use GreenFedora\Form\FormInterface;
use GreenFedora\Form\Field\Label;

use GreenFedora\Form\Field\Exception\InvalidArgumentException;

use GreenFedora\Filter\FilterInterface;
use GreenFedora\Validator\ValidatorInterface;

/**
 * Form field class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Field extends Html implements FieldInterface
{
    /**
     * Parent form.
     * @var FormInterface
     */
    protected $form = null;

    /**
     * Field value.
     * @var mixed
     */
    protected $value = '';

    /**
     * Label for autolabel.
     * @var Label
     */
    protected $label = null;

    /**
     * Wrap the field in something?
     * @var string|null
     */
    protected $wrap = null;

    /**
     * Allow auto wrap?
     * @var bool
     */
    protected $allowAutoWrap = false;

    /**
     * Filters.
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * Input filters.
     * @var FilterInterface[]
     */
    protected $inputFilters = [];

    /**
     * Validators.
     * @var ValidatorInterface[]
     */
    protected $validators = [];

    /**
     * Error.
     * @var string|null
     */
    protected $error = null;

    /**
     * Code to display after field.
     * @var string
     */
    protected $after = '';

    /**
     * Allow validation?
     * @var bool
     */
    protected $validationAllowed = true;

    /**
     * Inner wrap.
     * @var array|null
     */
    protected $innerWrap = null;

    /**
     * Constructor.
     * 
     * @param   FormInterface       $form           Parent form.
     * @param   string              $tag            HTML tag.
     * @param   array               $params         Parameters.
     * @param   bool                $autoLabel      Automatically add a label?
     * @param   bool                $allowAutoWrap  Allow auto wrapping?
     * @return  void
     * @throws  InvalidArgumentException
     */
    public function __construct(FormInterface $form, string $tag, array $params = [],
        bool $autoLabel = false, bool $allowAutoWrap = false)
    {
        $this->form = $form;
        if (array_key_exists('name', $params) and !array_key_exists('id', $params)) {
            $params['id'] = $params['name'];
        }
        if ($autoLabel) {
            if (!$params['label']) {
                throw new InvalidArgumentException(sprintf("Autolabel form fields must have the 'label' parameter (%s)", $this->tag));
            } else {
                $this->label = new Label($form, ['for' => $params['name']]);
                $this->label->setData($params['label']);
                unset($params['label']);
            }

            $this->n = $params['name'];
        }

        $this->allowAutoWrap = $allowAutoWrap;

        if ($this->allowAutoWrap) {
            if (array_key_exists('wrap', $params)) {
                $this->wrap = $params['wrap'];
                unset($params['wrap']);
            } else {
                $ar = $form->getAutoWrap();
                if (null !== $ar) {
                    $this->wrap = $ar;
                }
            }
        }

        parent::__construct($tag, $params);
    }

    /**
     * Set the name.
     * 
     * @param   string      $name   Name to set.
     * @return  FieldInterface
     */
    public function setName(string $name): FieldInterface
    {
        $this->setParam('name', $name);
        return $this;
    }

    /**
     * Get the name.
     * 
     * @return  string|null
     */
    public function getName(): ?string
    {
        if (!$this->hasParam('name')) {
            return null;
        }
        return $this->getParam('name');
    }

    /**
     * Set the field value.
     * 
     * @param   mixed   $value      Value to set.
     * @return  FieldInterface
     */
    public function setValue($value): FieldInterface
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get the value.
     * 
     * @return  mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add a validator.
     * 
     * @param   ValidatorInterface  $validator  New validator.
     * @return  FieldInterface 
     */
    public function addValidator(ValidatorInterface $validator): FieldInterface
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * Add a filter.
     * 
     * @param   FilterInterface     $filter     New filter.
     * @return  FieldInterface 
     */
    public function addFilter(FilterInterface $filter): FieldInterface
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Filter the field.
     * 
     * @param   mixed   $source     Source to filter.
     * @return  mixed
     */
    public function filter($source)
    {
        if (count($this->filters) > 0) {
            foreach ($this->filters as $filter) {
                $source = $filter->filter($source);
            }
        }
        return $source;
    }

    /**
     * Add an input filter.
     * 
     * @param   FilterInterface     $filter     New filter.
     * @return  FieldInterface 
     */
    public function addInputFilter(FilterInterface $filter): FieldInterface
    {
        $this->inputFilters[] = $filter;
        return $this;
    }

    /**
     * Filter the field for input.
     * 
     * @param   mixed   $source     Source to filter.
     * @return  mixed
     */
    public function inputFilter($source)
    {
        if (count($this->inputFilters) > 0) {
            foreach ($this->inputFilters as $filter) {
                $source = $filter->filter($source);
            }
        }
        return $source;
    }

    /**
     * Do we have validators.
     * 
     * @return  bool
     */
    public function hasValidators(): bool
    {
        return (count($this->validators) > 0);
    }

    /**
     * Validate the field.
     * 
     * @param   mixed   $source     Source to check.
     * @return  bool
     */
    public function validate($source): bool
    {
        if (!$this->validationAllowed) {
            return true;
        }
        if (count($this->validators) > 0) {
            $source = $this->filter($source);

            foreach ($this->validators as $validator) {
                if (!$validator->validate($source)) {
                    $this->error = $validator->getError();
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Disable validators.
     * 
     * @return  FieldInterface
     */
    public function disableValidators(): FieldInterface
    {
        $this->validationAllowed = false;
        return $this;
    }

    /**
     * Set the after stuff.
     * 
     * @param   string      $stuff      Stuff you want after the field.
     * @return  FieldInterface
     */
    public function setAfter(string $stuff): FieldInterface
    {
        $this->after = $stuff;
        return $this;
    }

    /**
     * Get the error.
     * 
     * @return  string|null
     */
    public function getError(): ?string
    {
        return $this->error;
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
        if ($this->form->getAutofocus() == $this->getName()) {
            $this->setParam('autofocus', true);
        }

        if (!empty($this->value)) {
            $this->setParam('value', $this->value);
        }

        $ret = '';

        if ($this->allowAutoWrap and $this->wrap) {
            $w = $this->form->createField($this->wrap . 'open', ['name' => $this->getName() . $this->wrap]);
            $ret .= $w->render();
        }


        if (null !== $this->label) {
            $ret .= $this->label->render();
        }

        //if ('checkbox' == $this->params['type']) {
        //    throw new \Exception($ret);
        //}

        $iw = null;
        if (null !== $this->innerWrap) {
            $html = new Html($this->innerWrap[0], $this->innerWrap[1]);
            $iw = $html->render(parent::render($data));
        } else {
            $iw = parent::render($data);
        }

        $ret .= $iw . PHP_EOL . $this->after;

        if ($this->allowAutoWrap and $this->wrap) {
            $w = $this->form->createField($this->wrap . 'close', ['name' => $this->getName() . $this->wrap]);
            $ret .= $w->render();
        }


        return $ret;
    }

}