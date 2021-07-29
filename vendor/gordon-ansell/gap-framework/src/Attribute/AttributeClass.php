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

use GreenFedora\Attribute\AttributeClassInterface;
use GreenFedora\Attribute\AttributeMethodInterface;
use GreenFedora\Attribute\AttributeMethod;
use GreenFedora\Attribute\AttributeInterface;
use GreenFedora\Attribute\Attribute;

/**
 * Process attributes.
 * 
 * This will be redundant after PHP 8.0.
 */
class AttributeClass extends \ReflectionClass implements AttributeClassInterface
{
    /**
     * Attributes.
     * @var AttributeInterface[]|null
     */
    protected $attributes = null;

    /**
     * Constructor.
     * 
     * @param   object|string   $objectOrClass  What to reflect.
     * @return  void
     */
    public function __construct($objectOrClass)
    {
        parent::__construct($objectOrClass);
    }

    /**
     * Get the constructor.
     * 
     * @return  AttributeMethodInterface|null
     */
    public function getConstructor(): ?AttributeMethodInterface
    {
        return new AttributeMethod($this->getName() . '::__construct');
    }

    /**
     * Get the attributes.
     * 
     * @param   string|null         $name
     * @param   int                 $flags
     * 
     * @return  AttributeInterface[]
     */
    public function getAttributes(?string $name = null, int $flags = 0): array
    {
        if (is_null($this->attributes)) {
            $this->attributes = [];
            $dc = $this->getDocComment();
            if (false !== $dc) {
                $this->attributes = Attribute::parseAttributes($dc);
            }
        }
        return $this->attributes;
    }
}
