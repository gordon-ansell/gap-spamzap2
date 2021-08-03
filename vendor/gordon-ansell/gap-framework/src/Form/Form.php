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

use GreenFedora\Form\FormInterface;
use GreenFedora\Form\FormPersistHandlerInterface;

use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Html\Html;

use GreenFedora\Form\Field\FieldInterface;
use GreenFedora\Form\Field\FieldsetOpen;
use GreenFedora\Form\Field\FieldsetClose;
use GreenFedora\Form\Field\DivOpen;
use GreenFedora\Form\Field\DivClose;
use GreenFedora\Form\Field\SpanOpen;
use GreenFedora\Form\Field\SpanClose;
use GreenFedora\Form\Field\Label;
use GreenFedora\Form\Field\Input;
use GreenFedora\Form\Field\Button;
use GreenFedora\Form\Field\Select;
use GreenFedora\Form\Field\RadioSet;
use GreenFedora\Form\Field\Checkbox;
use GreenFedora\Form\Field\CheckboxSet;
use GreenFedora\Form\Field\Errors;
use GreenFedora\Form\Field\Custom\Weight;

use GreenFedora\Form\Exception\InvalidArgumentException;
use GreenFedora\Form\Exception\OutOfBoundsException;

/**
 * Form class.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Form extends Html implements FormInterface
{
    /**
     * Form name.
     * @var string|null
     */
    protected $name = null;

    /**
     * Form action.
     * @var string
     */
    protected $action = '';

    /**
     * Form method.
     * @var string
     */
    protected $method = 'POST';

    /**
     * Persistence handler.
     * @var FormPersistHandlerInterface
     */
    protected $persist = null;

    /**
     * Fields.
     * @var array
     */
    protected $fields = [];

    /**
     * Raw fields.
     * @var array
     */
    protected $rawFields = [];

    /**
     * Auto wrap fields?
     * @var string|null
     */
    protected $autoWrap = null;

    /**
     * Form errors.
     * @var array
     */
    protected $errors = [];

    /**
     * Autofocus field.
     * @var string|null
     */
    protected $autofocus = null;

    /**
     * Last fields added.
     * @var array
     */
    protected $lastFields = [];

    /**
     * Use CSRF?
     * @var bool
     */
    protected $csrf = true;

    /**
     * Constructor.
     * 
     * @param   string                      $name       Form name,
     * @param   string                      $action     Action.
     * @param   FormPersistHandlerInterface $persist    Persistence handler.
     * @param   array                       $params     Parameters.
     * @param   string                      $method     Method.
     * @return  void
     */
    public function __construct(string $name, string $action, FormPersistHandlerInterface $persist = null, 
        array $params = [], string $method = 'POST')
    {
        $this->name = $name;

        $params['action'] = $action;
        $params['method'] = $method;

        parent::__construct('form', $params);

        $this->persist = $persist;
    }

    /**
     * Create a field.
     * 
     * @param   string|null                 $name       Field name.
     * @param   array                       $params     Field parameters.
     * 
     * @return  FieldInterface
     * @throws  InvalidArgumentException
     */
    public function createField(string $type, array $params = []): FieldInterface
    {
        $ret = null;
        $name = null;

        if (array_key_exists('name', $params)) {
            $name = $params['name'];
        } else {
            throw new InvalidArgumentException(sprintf("Form fields require a 'name' (type = %s)", $type));
        }

        switch (strtolower($type)) {

            case 'divopen':
                $ret = new DivOpen($this, $params);
                break;

            case 'divclose':
                $ret = new DivClose($this, $params);
                break;

            case 'spanopen':
                $ret = new SpanOpen($this, $params);
                break;

            case 'spanclose':
                $ret = new SpanClose($this, $params);
                break;

            case 'fieldsetopen':
                $ret = new FieldsetOpen($this, $params);
                break;

            case 'fieldsetclose':
                $ret = new FieldsetClose($this, $params);
                break;
        
            case 'label':
                $ret = new Label($this, $params);
                break;

            case 'input':
                $ret = new Input($this, $params);
                break;

            case 'inputtext':
                $params['type'] = "text";
                $ret = new Input($this, $params);
                break;

            case 'inputnumber':
                $params['type'] = "number";
                $ret = new Input($this, $params);
                break;
    
            case 'inputradio':
                $params['type'] = "radio";
                $ret = new Input($this, $params);
                break;
                
            case 'inputhidden':
                $params['type'] = "hidden";
                $ret = new Input($this, $params, false);
                break;
    
            case 'radioset':
                $ret = new RadioSet($this, $params);
                break;    

            case 'checkbox':
                $ret = new Checkbox($this, $params);
                break;    
    
            case 'checkboxset':
                $ret = new CheckboxSet($this, $params);
                break;    

            case 'select':
                $ret = new Select($this, $params);
                break;
    
            case 'button':
                $ret = new Button($this, $params);
                break;

            case 'buttonsubmit':
                $params['type'] = "submit";
                $ret = new Button($this, $params);
                break;

            case 'buttonsubmitautolabel':
                $params['type'] = "submit";
                $ret = new Button($this, $params, true);
                break;

            case 'errors':
                $ret = new Errors($this, $params);
                break;
    
            case 'weight':
                $ret = new Weight($this, $params);
                break;       
        
            default:
                throw new InvalidArgumentException(sprintf("'%s' is an invalid form field type", $type));
                break;
        }

        return $ret;
    }

    /**
     * Add a field.
     * 
     * @param   string|FieldInterface       $type       Field type or class instance.
     * @param   array                       $params     Field parameters.
     * 
     * @return  FieldInterface
     * @throws  InvalidArgumentException
     */
    public function addField($type, array $params = []): FieldInterface
    {
        $name = null;
        if ($type instanceof FieldInterface) {
            $name = $type->getName();
        } else {
            if (array_key_exists('name', $params)) {
                $name = $params['name'];
            }
        }

        if (null === $name) {
            throw new InvalidArgumentException(sprintf("Could not determine name for form field (type: %s)", $type));
        } else if (array_key_exists($name, $this->fields)) {
            throw new InvalidArgumentException(sprintf("A form field with the name '%s' already exists", $name));
        }

        if (false !== strpos($type, 'open')) {
            $this->lastFields[] = array('type' => $type, 'name' => $name);
        }

        return $this->setField($type, $params);
    }

    /**
     * Close the last field.
     * 
     * @return  FieldInterface 
     * @throws  OutOfBoundsException
     */
    public function closeField(): FieldInterface
    {
        if (0 == count($this->lastFields)) {
            throw new OutOfBoundsException("Nothing on the lastFields stack.");
        }

        $item = array_pop($this->lastFields);

        $name = $item['name'];
        $type = $item['type'];

        return $this->addField(str_replace('open', 'close', $type), 
            ['name' => str_replace('open', '', $name . 'close')]);
    }

    /**
     * Set a field.
     * 
     * @param   string|FieldInterface       $type       Field type or class instance.
     * @param   array                       $params     Field parameters.
     * 
     * @return  FieldInterface
     */
    public function setField($type, array $params = []): FieldInterface
    {
        $ret = null;
        $name = null;

        if ($type instanceof FieldInterface) {
            $ret = $type;
            $name = $type->getName();
        } else {
            $ret = $this->createField($type, $params);
            $name = $ret->getName();
        }

        $this->fields[$name] = $ret;
        return $this->fields[$name]; 
    }

    /**
     * Add a raw field.
     * 
     * @param   string  $field  Field spec.
     * @return  FormInterface
     */
    public function addRawField(string $field): FormInterface
    {
        $this->rawFields[] = $field;
        return $this;
    }

    /**
     * Get a field.
     * 
     * @param   string  $name   Name of field to get.
     * @return  FieldInterface
     * @throws  InvalidArgumentException
     */
    public function getField(string $name): FieldInterface
    {
        if (!array_key_exists($name, $this->fields)) {
            throw new InvalidArgumentException(sprintf("Form has no field called '%s'", $name));
        }
        return $this->fields[$name];
    }

    /**
     * Set the autofocus field.
     * 
     * @param   string  $field  Name of field to set.
     * @return  FormInterface
     * @throws  InvalidArgumentException
     */
    public function setAutofocus(string $field): FormInterface
    {
        if (array_key_exists($field, $this->fields)) {
            $this->autofocus = $field;
            return $this;
        }
        throw new InvalidArgumentException(sprintf("Cannot set autofocus to'%s' - field does not exist", $field));
    }

    /**
     * Get the autofocus field.
     * 
     * @return  string|null
     */
    public function getAutofocus(): ?string
    {
        return $this->autofocus;
    }

    /**
     * Set the autoWrap field.
     * 
     * @param   string          $type   Type of wrapping to do.
     * @return  FormInterface
     * @throws  InvalidArgumentException
     */
    public function setAutoWrap(string $type): FormInterface
    {
        $this->autoWrap = $type;
        return $this;
    }

    /**
     * Get the autowrap field.
     * 
     * @return  string|null
     */
    public function getAutoWrap(): ?string
    {
        return $this->autoWrap;
    }

    /**
     * Set CSRF.
     * 
     * @param   bool    $val    Value to set.
     * @return  FormInterface
     */
    public function setCsrf(bool $val): FormInterface
    {
        $this->csrf = $val;
        return $this;
    }

    /**
     * Get the persistence handler.
     * 
     * @return  FormPersistHandlerInterface
     */
    public function getPersisthandler(): FormPersistHandlerInterface
    {
        return $this->persist;
    }

    /**
     * Load persistence.
     * 
     * @param   ArrInterface    $target     Where to load them.
     * @return  FormInterface 
     */
    public function load(ArrInterface &$target): FormInterface
    {
        if (null !== $this->persist) {
            $this->persist->load($target);
        }

        foreach ($target as $k => $v) {
            if (array_key_exists($k, $this->fields)) {
                $this->fields[$k]->setValue($this->fields[$k]->inputFilter($v));
            }
        }

        return $this;
    }

    /**
     * Save persistence.
     * 
     * @param   ArrInterface    $source     Where to save them from.
     * @return  FormInterface 
     */
    public function save(ArrInterface $source): FormInterface
    {
        if (null !== $this->persist) {
            $this->persist->save($source);
        }

        foreach ($source as $k => $v) {
            if (array_key_exists($k, $this->fields)) {
                $this->fields[$k]->setValue($v);
            }
        }

        return $this;
    }

   /**
     * Check CSRF.
     * 
     * @return  bool
     */
    protected function checkCsrf(): bool
    {
        if (!$this->csrf) {
            return true;
        }

        $session = $this->persist->getSession();

        $token1 = $session->csrfToken1();
        if (!empty($_POST['token1'])) {
            if (!hash_equals($token1, $_POST['token1'])) {
                $this->errors[] = "Invalid CSRF token (1): " . $token1 . " != " . $_POST['token1'];
                return false;
            }
        } else {
            $this->error[] = "No CSRF token (1)";
            return false;
        }

        $token2 = $session->csrfToken2($this->name);
        if (!empty($_POST['token2'])) {
            if (!hash_equals($token2, $_POST['token2'])) {
                $this->errors[] = "Invalid CSRF token (2)";
                return false;
            }
        } else {
            $this->errors[] = "No CSRF token (2)";
            return false;
        }

        return true;
    }

    /**
     * Validate the form.
     * 
     * @param   array   $source     Source data to validate.
     * @return  bool
     */
    public function validate(array $source): bool
    {
        $status = true;
        $gotAf = false;

        if ($this->csrf) {
            if (!$this->checkCsrf()) {
                return false;
            }
        }

        foreach ($this->fields as $k => $v) {

            if ($v->hasValidators()) {

                $data = null;
                if (array_key_exists($k, $source)) {
                    $data = $source[$k];
                }

                if (!$v->validate($data)) {
                    $this->errors[] = $v->getError();
                    $status = false;
                    if (!$gotAf) {
                        $this->setAutofocus($v->getName());
                        $gotAf = true;
                    }
                }

            }

        }

        return $status;
    }

    /**
     * Do we have errors?
     * 
     * @return  bool 
     */
    public function hasErrors(): bool
    {
        return (count($this->errors) > 0);
    }

    /**
     * Get the errors.
     * 
     * @return  array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Add an error.
     * 
     * @param   string      $err    Error to add.
     * @return  FormInterface
     */
    public function addError(string $err): FormInterface
    {
        $this->errors[] = $err;
        return $this;
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
        $ret = '';
        $ret .= parent::renderOpen($extraParams);

        foreach ($this->fields as $field) {
            $ret .= $field->render();
        }

        if ($this->csrf) {
            $session = $this->persist->getSession();
            $token1 = $session->csrfToken1();
            $token2 = $session->csrfToken2($this->name);
            $ret .= "<input type='hidden' name='token1' value='" . $token1 . "' />";
            $ret .= "<input type='hidden' name='token2' value='" . $token2 . "' />";
        }

        if (0 !== count($this->rawFields)) {
            foreach ($this->rawFields as $f) {
                $ret .= $f;
            }
        }

        $ret .= "<input type='hidden' name='form-submitted' value='" . $this->name . "' />";

        $ret .= parent::renderClose();

        return $ret;
    }

    /**
     * Get the debug messages.
     * 
     * @return  array   
     */
    public function getDebugging(): array
    {
        $fd = $this->debugMsgs;

        foreach ($this->fields as $k => $v) {
            if ($v->hasDebugging()) {
                foreach ($v->getDebugging() as $d) {
                    $fd[] = array($d[0], $k . ': ' . $d[1]);
                }
            }
        }

        return $fd;
    }

}