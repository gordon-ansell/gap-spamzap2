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

/**
 * The abstract validator.
 */
abstract class AbstractValidator
{
    /**
     * Options.
     * @var iterable
     */
    protected $options = null;

    /**
     * Error.
     * @var string
     */
    protected $error = null;

    /**
     * Replacements.
     * @var array
     */
    protected $reps = [];

    /**
     * Message.
     * @var string
     */
    protected $msg = null;

    /**
     * Do the message.
     * @var bool
     */
    protected $doMsg = true;

    /**
     * Constructor.
     * 
     * @param   array           $reps       Replacements.
     * @param   iterable        $options    Validation options.
     * @param   string|null     $msg        Validator message template. 
     * @param   bool            $doMsg      Do the message?
     * @return  void 
     */
    public function __construct(array $reps = [], ?iterable $options = null, ?string $msg = null, bool $doMsg = true)
    {
        $this->options = $options;
        $this->reps = $reps;
        $this->doMsg = $doMsg;
        if (!is_null($msg)) {
            $this->msg = $msg;
        }
    }

    /**
     * Perform the validation.
     * 
     * @param   mixed       $data       Data to validate.
     * @return  bool                    True if it's valid, else false. 
     */
    abstract public function validate($data) : bool;

    /**
     * Set the validator message.
     * 
     * @param   string      $msg        Message to set.
     * @return  string                  Previous message.
     */
    public function setMsg(string $msg): string
    {
        $saved = $this->msg;
        $this->msg = $msg;
        return $saved;
    }

    /**
     * Get the error.
     * 
     * @return string                   Error message.
     */
    public function getError(): ?string
    {
        return $this->error;
    }
}
