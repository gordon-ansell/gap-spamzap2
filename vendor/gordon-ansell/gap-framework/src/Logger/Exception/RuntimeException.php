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
namespace GreenFedora\Logger\Exception;

use GreenFedora\Logger\Exception\ExceptionInterface;

/**
 * Runtime.
 * 
 * Exception thrown if an error which can only be found on runtime occurs. 
 * 
 * Runtime
 */
class RuntimeException extends \RuntimeException implements ExceptionInterface
{
}
