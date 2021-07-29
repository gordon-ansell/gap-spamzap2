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
namespace GreenFedora\Console\Exception;

use GreenFedora\Console\Exception\ExceptionInterface;

/**
 * Bad function call.
 * 
 * Exception thrown if a callback refers to an undefined function or if some arguments are missing.
 * 
 * Logic > BadFunctionCall
 */
class BadFunctionCallException extends \BadFunctionCallException implements ExceptionInterface
{
}
