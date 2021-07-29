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

use GreenFedora\Filter\FilterInterface;
use GreenFedora\Validator\ValidatorInterface;

/**
 * Form field interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface FieldInterface
{
    /**
     * Set the name.
     * 
     * @param   string      $name   Name to set.
     * @return  FieldInterface
     */
    public function setName(string $name): FieldInterface;

    /**
     * Get the name.
     * 
     * @return  string|null
     */
    public function getName(): ?string;

    /**
     * Set the field value.
     * 
     * @param   mixed   $value      Value to set.
     * @return  FieldInterface
     */
    public function setValue($value): FieldInterface;

    /**
     * Get the value.
     * 
     * @return  mixed
     */
    public function getValue();

    /**
     * Add a validator.
     * 
     * @param   ValidatorInterface  $validator  New validator.
     * @return  FieldInterface 
     */
    public function addValidator(ValidatorInterface $validator): FieldInterface;

    /**
     * Add a filter.
     * 
     * @param   FilterInterface     $filter     New filter.
     * @return  FieldInterface 
     */
    public function addFilter(FilterInterface $filter): FieldInterface;

    /**
     * Filter the field.
     * 
     * @param   mixed   $source     Source to filter.
     * @return  mixed
     */
    public function filter($source);

    /**
     * Do we have validators.
     * 
     * @return  bool
     */
    public function hasValidators(): bool;

    /**
     * Validate the field.
     * 
     * @param   mixed   $source     Source to check.
     * @return  bool
     */
    public function validate($source): bool;

    /**
     * Disable validators.
     * 
     * @return  FieldInterface
     */
    public function disableValidators(): FieldInterface;

    /**
     * Set the after stuff.
     * 
     * @param   string      $stuff      Stuff you want after the field.
     * @return  FieldInterface
     */
    public function setAfter(string $stuff): FieldInterface;

    /**
     * Get the error.
     * 
     * @return  string|null
     */
    public function getError(): ?string;

    /**
     * Render the field.
     * 
     * @param   string      $data           Data to use.
     * @param   array       $extraParams    Extra params for this render.
     * @return  string                      Rendered form HTML.
     */
    public function render(?string $data = null, ?array $extraParams = null): string;
}