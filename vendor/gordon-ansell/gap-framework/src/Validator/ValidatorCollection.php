<?php
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Validator;

use GreenFedora\Validator\ValidatorInterface;
use GreenFedora\Validator\ValidatorFactory;

/**
 * A collection of validators.
 */
class ValidatorCollection
{
    /**
     * The validators.
     * @var array
     */
    protected $validators = [];

    /**
     * Method.
     * @var string
     */
    protected $method = 'failonfirst';

    /**
     * Errors.
     * @var string[]
     */
    protected $errors = [];

    /**
     * Constructor.
     * 
     * @param   string  $method     How to handle failures.
     * @return  void
     */
    public function __construct(string $method = 'failonfirst')
    {
        $this->method = $method;
    }

    /**
     * Add a validator.
     * 
     * @param   string      $validator  Validator to build.
     * @param   array       $reps       Replacements.
     * @param   iterable    $options    Validation options.
     * @return  ValidatorCollection
     */
    public function add(string $validator, array $reps = [], ?iterable $options = null): ValidatorCollection
    {
        $this->validators[] = ValidatorFactory::build($validator, $reps, $options);
        return $this;
    }

    /**
     * Perform the validation.
     * 
     * @param   mixed       $data       Data to validate.
     * @return  bool                    True if it's valid, else false. 
     */
    public function validate($data) : bool
    {
        $this->errors = [];
        $ret = true;
        foreach ($this->validators as $validator) {
            if (!$validator->validate($data)) {
                $this->errors[] = $validator->getError();
                $ret = false;
                if ('failonfirst' === $this->method) {
                    return false;
                }
            }
        }
        return $ret;
    }

    /**
     * Get the errors.
     * 
     * @return string[]                    Error messages.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error.
     * 
     * @return string|null                 Error message or null.
     */
    public function getFirstError(): ?string
    {
        if (isset($this->errors[0])) {
            return $this->errors[0];
        }
        return null;
    }
}