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

use GreenFedora\Attribute\AttributeInterface;

/**
 * Attribute method.
 * 
 * This will be redundant after PHP 8.0.
 */
interface AttributeMethodInterface
{
    /**
     * Get the attributes.
     * 
     * @param   string|null         $name
     * @param   int                 $flags
     * 
     * @return  AttributeInterface[]
     */
    public function getAttributes(?string $name = null, int $flags = 0): array;
}
