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
namespace GreenFedora\Finder\Exception;

use GreenFedora\Finder\Exception\ExceptionInterface;

/**
 * Out of bounds.
 * 
 * Exception thrown if a value is not a valid key. 
 * This represents errors that cannot be detected at compile time.
 * 
 * Runtime > OutOfBounds
 */
class OutOfBoundsException extends \OutOfBoundsException implements ExceptionInterface
{
}
