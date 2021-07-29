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
namespace GreenFedora\Finder\Filter\Exception;

use GreenFedora\Finder\Filter\Exception\ExceptionInterface;

/**
 * Logic.
 * 
 * Exception that represents errors in the program logic. 
 * This kind of exception should lead directly to a fix in your code.
 * 
 * Logic
 */
class LogicException extends \LogicException implements ExceptionInterface
{
}