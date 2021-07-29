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
namespace GreenFedora\Stdlib\Text\Exception;

use GreenFedora\Stdlib\Text\Exception\ExceptionInterface;

/**
 * Out of range.
 * 
 * Exception thrown when an illegal index was requested. 
 * This represents errors that should be detected at compile time.
 * 
 * Logic > OutOfRange
 */
class OutOfRangeException extends \OutOfRangeException implements ExceptionInterface
{
}
