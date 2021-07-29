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
namespace GreenFedora\Attribute;

/**
 * Individual attribute interface.
 * 
 * This will be redundant after PHP 8.0.
 */
interface AttributeInterface
{
    /**
     * Get the attribute name.
     * 
     * @return  string
     */
    public function getName(): string;

    /**
     * Get the attribute arguments.
     * 
     * @return  array
     */
    public function getArguments(): array;
}
