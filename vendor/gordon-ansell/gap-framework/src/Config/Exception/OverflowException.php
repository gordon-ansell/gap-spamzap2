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
namespace GreenFedora\Config\Exception;

use GreenFedora\Config\Exception\ExceptionInterface;

/**
 * Overflow.
 * 
 * Exception thrown when adding an element to a full container.
 * 
 * Runtime > Overflow
 */
class OverflowException extends \OverflowException implements ExceptionInterface
{
}
