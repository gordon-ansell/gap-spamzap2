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

use GreenFedora\Stdlib\Env\EnvInterface;
use GreenFedora\Stdlib\Env\Exception\RuntimeException;

/**
 * Gets the environment variables.
 */
class Env implements EnvInterface
{
    /**
     * Base path of the application.
     * @var string
     */
    protected $basePath = null;

    /**
     * The env filename.
     * @var string
     */
    protected $envFilename = '.env';

    /**
     * Overwrite fields?
     * @var bool
     */
    protected $overwrite = false;

    /**
     * The actual data.
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     * 
     * @param   string  $basePath       Base application path.
     * @param   string  $envFilename    .env file name.
     * @param   bool    $overwrite      Overwrite samely named fields?
     * @param   bool    $autoload       Automatically losd up?
     * @return  void
     */
    public function __construct(string $basePath, string $envFilename = '.env', 
    bool $overwrite = false, bool $autoload = true)
    {
        $this->basePath = rtrim($basePath, "\/");
        $this->envFilename = trim($envFilename, "\/");
        $this->overwrite = $overwrite;
        if ($autoload) {
            $this->load();
        }
    }

    /**
     * Load the environment variables.
     * 
     * @return  void
     * @throws  RuntimeException
     */
    public function load(): void
    {
        // Standard environment variables.
        $this->data = getenv();
        
        // Load anything from the .env file.
        $fn = $this->basePath . DIRECTORY_SEPARATOR . $this->envFilename;
        if (file_exists($fn)) {
            $lines = file($fn, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

            foreach ($lines as $line) {
                $pos = strpos($line, '=');
                if (false === $pos) {
                    throw new RuntimeException("Invalid line in .env, there must be an '=' separating variable name and value.");
                }

                $key = substr($line, 0, $pos);
                $value = substr($line, $pos + 1);

                $this->addVar($key, $value);

            }
        } 
    }

    /**
     * Add a single variable.
     * 
     * @param   string  $key    Key.
     * @param   string  $value  Value.
     * @return  EnvInterface
     * @throws  LogicException
     */
    public function addVar(string $key, string $value): EnvInterface
    {
        $key = trim($key);
                
        if (!$this->overwrite and array_key_exists($key, $this->data)) {
            throw new RuntimeException(sprintf("An environment variable called '%s' already exists and overwrite = false. This is a name clash between PHP's native environment variables and what you have in the '.env' file.", $key));
        }
        
        $value = trim($value);

        $forceStr = false;
        $forceBool = false;
        
        if ('"' === $value[0] and '"' === $value[-1]) {
            $value = substr($value, 1, strlen($value) - 2);
            $forceStr = true;
        } else if ("false" === $value) {
            $value = false;
            $forceBool = true;
        } else if ("true" === $value) {
            $value = true;
            $forceBool = true;
        }

        if ($forceStr) {
            $this->data[$key] = strval($value);
        } else if ($forceBool) {
            $this->data[$key] = $value;
        } else if (is_numeric($value)) {
            if (false === strpos($value, '.')) {
                $this->data[$key] = intval($value);
            } else {
                $this->data[$key] = floatval($value);
            }
        } else if (is_string($value)) {
            $this->data[$key] = strval($value);
        } else {
            $this->data[$key] = $value;
        } 

        return $this;
    }

    /**
     * Get the data.
     * 
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get a single key.
     * 
     * @param   string  $key        Key.
     * @param   mixed   $default    Default if key not found.
     * @return  mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return $default;
    }
}
