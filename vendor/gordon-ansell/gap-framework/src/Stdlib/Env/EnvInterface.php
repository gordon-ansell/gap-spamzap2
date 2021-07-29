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
namespace GreenFedora\StdLib\Env;

/**
 * Interface for the Env class.
 */
interface EnvInterface
{
    /**
     * Load the environment variables.
     * 
     * @return  void
     * @throws  RuntimeException
     */
    public function load(): void;

    /**
     * Add a single variable.
     * 
     * @param   string  $key    Key.
     * @param   string  $value  Value.
     * @return  EnvInterface
     * @throws  RuntimeException
     */
    public function addVar(string $key, string $value): EnvInterface;

    /**
     * Get the data.
     * 
     * @return array
     */
    public function getData(): array;

    /**
     * Get a single key.
     * 
     * @param   string  $key        Key.
     * @param   mixed   $default    Default if key not found.
     * @return  mixed
     */
    public function get(string $key, $default = null);
}
