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

/**
 * Command line conditional modification interface.
 */
interface ConditionalInterface
{
    /**
     * Do we match?
     * 
     * @param   mixed   $command        Command value.
     * @param   mixed   $action         Action value.
     * @return  bool
     */
    public function match($command, $action): bool;

    /**
     * Check the conditional.
     * 
     * @return  string|null
     */
    public function check(): ?string;
}
