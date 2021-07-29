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
 * Range.
 * 
 * Exception thrown to indicate range errors during program execution. 
 * Normally this means there was an arithmetic error other than under/overflow. 
 * This is the runtime version of DomainException.
 * 
 * Runtime > Range
 */
class RangeException extends \RangeException implements ExceptionInterface
{
}
