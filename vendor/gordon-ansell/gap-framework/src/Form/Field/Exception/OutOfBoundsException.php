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
namespace GreenFedora\Form\Field\Exception;

use GreenFedora\Form\Field\Exception\ExceptionInterface;

/**
 * Invalid argument passed to form field.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class OutOfBoundsException extends \OutOfBoundsException implements ExceptionInterface
{
}
