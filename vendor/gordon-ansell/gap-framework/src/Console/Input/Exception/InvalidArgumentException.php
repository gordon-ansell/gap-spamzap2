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
namespace GreenFedora\Console\Input\Exception;

use GreenFedora\Console\Input\Exception\ExceptionInterface;

/**
 * Invalid argument.
 * 
 * Exception thrown if an argument is not of the expected type.
 * 
 * Logic > InvalidArgument
 */
class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
