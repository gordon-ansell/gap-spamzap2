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
namespace <namespace>\<component>\Exception;

use <namespace>\<component>\Exception\ExceptionInterface;

/**
 * Domain.
 * 
 * Exception thrown if a value does not adhere to a defined valid data domain.
 * 
 * Logic > Domain
 */
class DomainException extends \DomainException implements ExceptionInterface
{
}
