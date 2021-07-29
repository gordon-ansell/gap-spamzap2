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
namespace GreenFedora\Db\Sql\Part;

use GreenFedora\Db\Driver\Stmt\Stmt;
use GreenFedora\Db\Driver\Stmt\StmtInterface;

use GreenFedora\Db\Sql\AbstractSql;
use GreenFedora\Db\DbInterface;

/**
 * SQL value class.
 */
class Value
{
    /**
     * Value.
     * @var mixed
     */
    protected $value = null;

    /**
     * Bind parameter.
     * @var string
     */
    protected $bind = null;

    /**
     * Constructor.
     *
     * @param   mixed       $value      Value.
     * @return  void
     */
    public function __construct($value)
    {
        $this->value = $value;
        $this->bind = $this->randomBind();
    }

    /**
     * Generate a random bind.
     *
     * @return  string
     */
    protected function randomBind() : string
    {
        return ':v' . bin2hex(random_bytes(10));
    }

    /**
     * Bind this value.
     *
     * @param   StmtInterface        $stmt       Statement.
     * @return  array                            [placeholder, value]   
     */
    public function bind(StmtInterface $stmt): array
    {
        $stmt->bindValue($this->bind, $this->value);
        return [$this->bind, $this->value];
    }

    /** 
     * Get the actual value.
     * 
     * @return  mixed
     */
    public function rawVal()
    {
        return $this->value;
    }

    /**
     * Resolve this clause.
     *
     * @param   bool        $asBind     Resolve it as a bind?
     * @return  string
     */
    public function resolve(bool $asBind = true) : string
    {
        if ($asBind) {
            return $this->bind;
        } else {
            return $this->value;
        }
    }
}
