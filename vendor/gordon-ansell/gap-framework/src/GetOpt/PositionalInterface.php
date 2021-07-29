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
namespace GreenFedora\GetOpt;

use GreenFedora\GetOpt\ParameterInterface;

/**
 * Interface for a positional parameter.
 */
interface PositionalInterface extends ParameterInterface
{
    /**
     * Add a conditional.
     * 
     * @param   string      $whereTest      Must be 'command' or 'action'.
     * @param   mixed       $whereValue     What value to test.
     * @param   int         $flags          What we're actually doing.
     * @return  PositionalInterface
     */
    public function addConditional(string $whereTest, $whereValue, int $flags = 1): PositionalInterface;

    /**
     * Is this an array?
     * 
     * @return  bool
     */
    public function isArray(): bool;
 
    /**
     * Is this the command?
     * 
     * @return  bool
     */
    public function isCommand(): bool;

    /**
     * Set this as the command.
     * 
     * @return  PositionalInterface
     */
    public function setAsCommand(): PositionalInterface;

    /**
     * Set this as the action.
     * 
     * @return  PositionalInterface
     */
    public function setAsAction(): PositionalInterface;

    /**
     * Is this the action?
     * 
     * @return  bool
     */
    public function isAction(): bool;

}
