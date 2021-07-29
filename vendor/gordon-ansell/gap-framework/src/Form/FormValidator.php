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
namespace GreenFedora\Form;

use GreenFedora\Validator\ValidatorCollection;
use GreenFedora\Validator\ValidatorInterface;
use GreenFedora\Filter\FilterCollection;
use GreenFedora\Filter\FilterInterface;

use GreenFedora\Form\Exception\InvalidArgumentException;

/**
 * Form validator.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class FormValidator
{
    /**
     * Validators
     * @var array
     */
    protected $validators = [];

    /**
     * Filters.
     * @var array
     */
    protected $filters = [];

    /**
     * Field that failed.
     * @var string
     */
    protected $failedField = null;

    /**
     * Constructor.
     * 
     * @return  void 
     */
    public function __construct()
    {
    } 

    /**
     * Add a validator.
     * 
     * @param   string              $field      Form field.
     * @param   ValidatorInterface  $validator  Validator.
     * @return  self
     */
    public function addValidator(string $field, ValidatorInterface $validator): self
    {
        if (!array_key_exists($field, $this->validators)) {
            $this->validators[$field] = new ValidatorCollection();
        }
        $this->validators[$field]->add($validator);
        return $this;
    }

    /**
     * Add a ilter.
     * 
     * @param   string              $field      Form field.
     * @param   FilterInterface     $filter     Filter.
     * @return  self
     */
    public function addFilter(string $field, FilterInterface $filter): self
    {
        if (!array_key_exists($field, $this->filters)) {
            $this->filters[$field] = new FilterCollection();
        }
        $this->filters[$field]->add($filter);
        return $this;
    }

    /**
     * Do the validation.
     * 
     * @param   array       $source             Source to check.
     * @param   array       $fields             Fields to check.
     * @return  string|null                     Error message or null if all is well. 
     */
    public function validate(array $source, array $fields): ?string
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $source)) {
                print_r($source);
                throw new InvalidArgumentException(sprintf("Field '%s' not found in source for form validation", $field));
            } else {
                $f = $source[$field];

                if (array_key_exists($field, $this->filters)) {
                    $f = $this->filters[$field]->filter($f);
                }

                if (array_key_exists($field, $this->validators)) {
                    if (!$this->validators[$field]->validate($f)) {
                        $this->failedField = $field;
                        return $this->validators[$field]->getError();
                    }
                }
            }
        }

        return null;
    }

    /**
     * Get the failed field name.
     * 
     * @return  string      Failed field.
     */
    public function getFailedField(): ?string
    {
        return $this->failedField;
    }
}