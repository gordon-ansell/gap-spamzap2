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
 * Numeric between validator.
 */
class NumericBetweenValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * Message.
     * @var string
     */
    protected $msg = "The '%s' field must be between %s and %s.";

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
        
        if (!is_numeric($data)) {
            $this->error = vsprintf("'%s' must be numeric", $this->reps);
            return false;
        }

        if (!$this->options['high']) {
            throw new InvalidArgumentException("The NumericBetween validation requires the 'high' option.");
        }
        if (!$this->options['low']) {
            throw new InvalidArgumentException("The NumericBetween validation requires the 'low' option.");
        }

        if ($data < $this->options['low'] or $data > $this->options['high']) {
            $reps = $this->reps;
            $reps[] = $this->options['low'];
            $reps[] = $this->options['high'];
            $this->error = vsprintf($this->msg, $reps);
            return false;
        }

        return true;
    }
}