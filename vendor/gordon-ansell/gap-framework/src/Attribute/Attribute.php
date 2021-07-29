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
namespace GreenFedora\Attribute;

use GreenFedora\Attribute\AttributeInterface;

/**
 * Individual attribute.
 * 
 * This will be redundant after PHP 8.0.
 */
class Attribute implements AttributeInterface
{
    /**
     * Attribute name.
     * @var string
     */
    protected $name = null;

    /**
     * Attribute arguments.
     * @var array
     */
    protected $arguments = [];

    /**
     * Constructor.
     * 
     * @param   string   $name      Attribute name.
     * @param   array    $arguments Attribute arguments.
     * @return  void
     */
    public function __construct(string $name, array $arguments = [])
    {
        $this->name = trim($name);
        $this->arguments = $arguments;
    }

    /**
     * Get the attribute name.
     * 
     * @return  string
     */
    public function getName(): string
    {
        return $this->name;   
    }

    /**
     * Get the attribute arguments.
     * 
     * @return  array
     */
    public function getArguments(): array
    {
        return $this->arguments;   
    }
    
    /**
     * Parse the attributes.
     * 
     * @param   string|null     $docComments    Comments to parse.
     * @return  Attribute[]                     Attributes found.
     */
    public static function parseAttributes(?string $docComments): array
    {
        $attributes = [];

        $pattern = "~\#\[([a-zA-Z]+[a-zA-Z0-9]*)\s*(\(.*\))?\]~";
        
        if ($docComments) {

            $lines = explode("\n", $docComments);

            foreach($lines as $line) {

                if (preg_match($pattern, $line, $matches)) {

                    $name = null;
                    $arguments = [];
                    if (isset($matches[1])) {
                        $name = $matches[1];

                        if (isset($matches[2])) {
                            $trimmed = trim(trim($matches[2], '()'));
                            if ('' !== $trimmed) {
                                $arguments = explode(',', $trimmed);
                                array_walk($arguments, function(&$val, $key) {$val = trim($val);});
                            }
                        }
                    }

                    $attributes[] = new Attribute($name, $arguments);
                }

            }
        }

        return $attributes;

    }
}
