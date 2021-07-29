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
namespace GreenFedora\Application\Exception;

use GreenFedora\Application\Exception\ExceptionInterface;

/**
 * Unexpected value.
 * 
 * Exception thrown if a value does not match with a set of values. 
 * Typically this happens when a function calls another function and expects the return value 
 * to be of a certain type or value not including arithmetic or buffer related errors.
 * 
 * Runtime > UnexpectedValue
 */
class UnexpectedValueException extends \UnexpectedValueException implements ExceptionInterface
{
}
