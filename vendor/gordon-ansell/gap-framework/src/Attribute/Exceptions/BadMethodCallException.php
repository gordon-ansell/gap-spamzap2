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
namespace GreenFedora\Attribute\Exception;

use GreenFedora\Attribute\Exception\ExceptionInterface;

/**
 * Bad method call.
 * 
 * Exception thrown if a callback refers to an undefined method or if some arguments are missing.
 * 
 * Logic > BadFunctionCall > BadMethodCall
 */
class BadMethodCallException extends \BadMethodCallException implements ExceptionInterface
{
}
