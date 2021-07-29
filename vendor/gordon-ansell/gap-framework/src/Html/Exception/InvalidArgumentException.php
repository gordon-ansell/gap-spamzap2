<?php

/**
 * This file is part of the GordyAnsell GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Html\Exception;

use GreenFedora\Html\Exception\ExceptionInterface;

/**
 * Invalid argument passed to Form.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
