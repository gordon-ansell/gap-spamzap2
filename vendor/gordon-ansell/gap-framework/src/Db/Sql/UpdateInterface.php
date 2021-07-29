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
namespace GreenFedora\Db\Sql;

use GreenFedora\Db\Driver\Stmt\StmtInterface;
use GreenFedora\Db\Sql\Part\WhereUserInterface;

/**
 * SQL update class interface.
 */
interface UpdateInterface extends WhereUserInterface
{
    /**
     * Get the SQL.
     *
     * @return  string
     */
    public function getSql() : string;

    /**
     * Prepare.
     * 
     * @return  StmtInterface
     */
    public function prepare() : ?StmtInterface;

    /**
     * Execute.
     *
     * @return  bool
     */
    public function execute();
}
