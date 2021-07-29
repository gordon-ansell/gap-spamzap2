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

use GreenFedora\IP\IPAddress;
use GreenFedora\Validator\AbstractValidator;
use GreenFedora\Validator\ValidatorInterface;

/**
 * IP Address validator where it might be a CIDR.
 */
class IPAddressPossibleCIDRValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * Message.
     * @var string
     */
    protected $msg = "The '%s' field must be a valid IP address or CIDR.";

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

        if (false !== strpos($data, '/')) {
            $sp = explode('/', $data);
            if (!IPAddress::validate($sp[0])) {
                if ($this->doMsg) $this->error = vsprintf($this->msg, $this->reps);
                return false;
            }
            if (!is_numeric($sp[1])) {
                if ($this->doMsg) $this->error = vsprintf($this->msg, $this->reps);
                return false;
            }
            $icidr = intval($sp[1]);
            if ($icidr > 32) {
                if ($this->doMsg) $this->error = vsprintf($this->msg, $this->reps);
                return false;
            }
        } else if (!IPAddress::validate($data)) {
            if ($this->doMsg) $this->error = vsprintf($this->msg, $this->reps);
            return false;
        }
        
        return true;
    }
}