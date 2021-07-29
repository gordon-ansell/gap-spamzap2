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

use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Form\FormPersistHandlerInterface;
use GreenFedora\Form\Field\FieldInterface;


/**
 * Form interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface FormInterface
{
    /**
     * Create a field.
     * 
     * @param   string|null                 $name       Field name.
     * @param   array                       $params     Field parameters.
     * 
     * @return  FieldInterface
     * @throws  InvalidArgumentException
     */
    public function createField(string $type, array $params = []): FieldInterface;

    /**
     * Add a field.
     * 
     * @param   string|FieldInterface       $type       Field type or class instance.
     * @param   array                       $params     Field parameters.
     * 
     * @return  FieldInterface
     * @throws  InvalidArgumentException
     */
    public function addField($type, array $params = []): FieldInterface;
 
    /**
     * Close the last field.
     * 
     * @return  FieldInterface 
     * @throws  OutOfBoundsException
     */
    public function closeField(): FieldInterface;

    /**
     * Set a field.
     * 
     * @param   string|FieldInterface       $type       Field type or class instance.
     * @param   array                       $params     Field parameters.
     * 
     * @return  FieldInterface
     */
    public function setField($type, array $params = []): FieldInterface;

    /**
     * Add a raw field.
     * 
     * @param   string  $field  Field spec.
     * @return  FormInterface
     */
    public function addRawField(string $field): FormInterface;
    
    /**
     * Get a field.
     * 
     * @param   string  $name   Name of field to get.
     * @return  FieldInterface
     * @throws  InvalidArgumentException
     */
    public function getField(string $name): FieldInterface;

    /**
     * Set the autofocus field.
     * 
     * @param   string  $field  Name of field to set.
     * @return  FormInterface
     * @throws  InvalidArgumentException
     */
    public function setAutofocus(string $field): FormInterface;

    /**
     * Get the autofocus field.
     * 
     * @return  string|null
     */
    public function getAutofocus(): ?string;

    /**
     * Set the autoWrap field.
     * 
     * @param   string          $type   Type of wrapping to do.
     * @return  FormInterface
     * @throws  InvalidArgumentException
     */
    public function setAutoWrap(string $type): FormInterface;

    /**
     * Get the autowrap field.
     * 
     * @return  string|null
     */
    public function getAutoWrap(): ?string;

    /**
     * Set CSRF.
     * 
     * @param   bool    $val    Value to set.
     * @return  FormInterface
     */
    public function setCsrf(bool $val): FormInterface;

    /**
     * Get the persistence handler.
     * 
     * @return  FormPersistHandlerInterface
     */
    public function getPersisthandler(): FormPersistHandlerInterface;

    /**
     * Load persistence.
     * 
     * @param   ArrInterface    $target     Where to load them.
     * @return  FormInterface 
     */
    public function load(ArrInterface &$target): FormInterface;

    /**
     * Save persistence.
     * 
     * @param   ArrInterface    $source     Where to save them from.
     * @return  FormInterface 
     */
    public function save(ArrInterface $source): FormInterface;

    /**
     * Validate the form.
     * 
     * @param   array   $source     Source data to validate.
     * @return  bool
     */
    public function validate(array $source): bool;

    /**
     * Do we have errors?
     * 
     * @return  bool 
     */
    public function hasErrors(): bool;

    /**
     * Get the errors.
     * 
     * @return  array
     */
    public function getErrors(): array;

    /**
     * Add an error.
     * 
     * @param   string      $err    Error to add.
     * @return  FormInterface
     */
    public function addError(string $err): FormInterface;

    /**
     * Render the field.
     * 
     * @param   string      $data           Data to use.
     * @param   array       $extraParams    Extra params for this render.
     * @return  string                      Rendered form HTML.
     */
    public function render(?string $data = null, ?array $extraParams = null): string;

    /**
     * Get the debug messages.
     * 
     * @return  array   
     */
    public function getDebugging(): array;

}