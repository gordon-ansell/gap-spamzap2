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
namespace GreenFedora\Validator;

use GreenFedora\Validator\ValidatorInterface; 
use GreenFedora\Validator\Exception\InvalidArgumentException;

/**
 * The validator factory.
 */
class ValidatorFactory
{
    /**
     * Build a validator.
     * 
     * @param   string|int      $validator  Validator to build.
     * @param   array           $reps       Replacements.
     * @param   iterable        $options    Validation options.
     * @return  ValidatorInterface 
     */
    public static function build($validator, array $reps = [], ?iterable $options = null): ValidatorInterface
    {
        $class = $validator;
        if (!class_exists($class)) {
            $class = '\\GreenFedora\\Validator\\' . $validator . 'Validator'::class;
            if (!class_exists($class)) {
                throw new InvalidArgumentException(sprintf("Unable to build '%s' validator. Class '%s' not found.", 
                    $validator, $class));
            }
        }

        return new $class($reps, $options);
    }
}