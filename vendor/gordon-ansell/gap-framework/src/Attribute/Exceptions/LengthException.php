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
 * Length.
 * 
 * Exception thrown if a length is invalid.
 * 
 * Logic > Length
 */
class LengthException extends \LengthException implements ExceptionInterface
{
}