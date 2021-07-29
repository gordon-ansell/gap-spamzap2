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
namespace GreenFedora\GetOpt;

use GreenFedora\GetOpt\ParameterInterface;
use GreenFedora\GetOpt\Exception\InvalidArgumentException;

/**
 * Command line conditional modification.
 */
abstract class AbstractConditional
{
    /**
     * Conditional flags.
     */
    const   COND_COMPULSORY =   1;

    /**
     * Parent.
     * @var ParameterInterface
     */
    protected $parent = null;

    /**
     * Where test.
     * @var string
     */
    protected $whereTest = null;

    /**
     * Where value.
     * @var mixed
     */
    protected $whereValue = null;

    /**
     * Flags.
     * @var int
     */
    protected $flags = 0;

    /**
     * Constructor.
     * 
     * @param   ParameterInterface      $parent         Parent positional or option.
     * @param   string                  $whereTest      Must be 'command' or 'action'.
     * @param   mixed                   $whereValue     What value to test.
     * @param   int                     $flags          What we're actually doing.
     * @return  void
     */
    public function __construct(ParameterInterface $parent, string $whereTest, $whereValue, int $flags = 0)
    {
        if (!in_array($whereTest, ['command', 'action'])) {
            throw new InvalidArgumentException("A GetOpt whereTest must be 'command' or 'action'.");
        }
        $this->parent = $parent;
        $this->whereTest = $whereTest;
        $this->whereValue = $whereValue;
        $this->flags = $flags;
    }

    /**
     * Do we match?
     * 
     * @param   mixed   $command        Command value.
     * @param   mixed   $action         Action value.
     * @return  bool
     */
    public function match($command, $action): bool
    {
        return (('command' === $this->whereTest and $command === $this->whereValue) 
            or ('action' === $this->whereTest and $action === $this->whereValue));
    }

    /**
     * Check the conditional.
     * 
     * @return  string|null
     */
    abstract public function check(): ?string;

}
