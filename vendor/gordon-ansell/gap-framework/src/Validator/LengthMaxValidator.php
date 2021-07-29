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

use GreenFedora\Validator\AbstractValidator;
use GreenFedora\Validator\ValidatorInterface;
use GreenFedora\Validator\Exception\InvalidArgumentException;

/**
 * Maximum length validator.
 */
class LengthMaxValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * Message.
     * @var string
     */
    protected $msg = "The '%s' field must be no longer than %s characters.";

    /**
     * Perform the validation.
     * 
     * @param   mixed       $data       Data to validate.
     * @return  bool                    True if it's valid, else false. 
     */
    public function validate($data) : bool
    {
        if (is_null($data) or empty($data)) {
            return true;
        }
        
        if (!$this->options['maxlength']) {
            throw new InvalidArgumentException("The LengthMax validation requires the 'maxlength' option.");
        }

        if (strlen($data) > $this->options['maxlength']) {
            $reps = $this->reps;
            $reps[] = $this->options['maxlength'];
            $this->error = vsprintf($this->msg, $reps);
            return false;
        }

        return true;
    }
}