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

/**
 * Alphanumeric validator.
 */
class AlphaNumericValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * Message.
     * @var string
     */
    protected $msg = "The '%s' field must only contain alphanumeric characters.";

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

        if (!ctype_alnum($data)) {
            $this->error = vsprintf($this->msg, $this->reps);
            return false;
        }
        return true;
    }
}