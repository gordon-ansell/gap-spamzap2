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

use GreenFedora\GetOpt\AbstractConditional;

/**
 * Command line conditional modification.
 */
class ConditionalOption extends AbstractConditional implements ConditionalInterface
{
    /**
     * Check the conditional.
     * 
     * @return  string|null
     */
    public function check(): ?string
    {
        if (self::COND_COMPULSORY === $this->flags) {
            if (is_null($this->parent->getValue()) or is_bool($this->parent->getValue())) {
                return sprintf("You must specify a value for the '%s' option.", $this->parent->getName());
            }
        }
        return null;
    }

}
