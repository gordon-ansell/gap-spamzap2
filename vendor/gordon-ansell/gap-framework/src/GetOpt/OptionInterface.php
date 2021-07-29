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
 * Option interface.
 */
interface OptionInterface extends ParameterInterface
{
    /**
     * Add a conditional.
     * 
     * @param   string      $whereTest      Must be 'command' or 'action'.
     * @param   mixed       $whereValue     What value to test.
     * @param   int         $flags          What we're actually doing.
     * @return  OptionInterface
     */
    public function addConditional(string $whereTest, $whereValue, int $flags = 1): OptionInterface;

    /**
     * Get the shortcut.
     * 
     * @return  string|null
     */
    public function getShortcut(): ?string;

    /**
     * Do we have a shortcut? If so, what is it?
     * 
     * @param   string|null     $test   If we want to test a particular shortcut, pass non-null.
     * @return  bool
     */
    public function hasShortcut(?string $test = null): bool;

    /**
     * Do we have an argument name.
     * 
     * @return  bool
     */
    public function hasArgumentName(): bool;

    /**
     * Get the argument name.
     * 
     * @return  string|null
     */
    public function getArgumentName(): ?string;

    /**
     * Has an argument?
     * 
     * @return  bool
     */
    public function hasArgument(): bool;

}
