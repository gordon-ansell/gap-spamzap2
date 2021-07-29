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
namespace GreenFedora\GetOpt\Exception;

use GreenFedora\GetOpt\Exception\ExceptionInterface;

/**
 * Underflow.
 * 
 * Exception thrown when performing an invalid operation on an empty container, such as removing an element.
 * 
 * Runtime > Underflow
 */
class UnderflowException extends \UnderflowException implements ExceptionInterface
{
}
