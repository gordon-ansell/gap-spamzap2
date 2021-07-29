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
namespace GreenFedora\Container\Exception;

use GreenFedora\Container\Exception\ExceptionInterface;

/**
 * Bind error.
 * 
 * Exception thrown during bind.
 * 
 * Logic > BindError
 */
class BindErrorException extends \LogicException implements ExceptionInterface
{
}
