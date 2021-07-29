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
namespace GreenFedora\Html\TableMaker;

use GreenFedora\Html\HtmlInterface;
use GreenFedora\Html\TableMaker\TableMakerInterface;

/**
 * Table <thead>.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface THeadInterface extends HtmlInterface 
{
    /**
     * Get the parent table.
     * 
     * @return  TableMakerInterface
     */
    public function getTable(): TableMakerInterface;

    /**
     * Add a column.
     * 
     * @param   string          $title      Column title.
     * @param   string          $name       Column name.
     * @param   array|string    $params     Parameters.
     * @return  THeadInterface    
     * @throws  InvalidArgumentException
     */
    public function addColumn(string $title, string $name = null, $params = []): THeadInterface;

    /**
     * See if we have a particular column.
     * 
     * @param   string|int      $name     Column name, which could be a number.
     * @return  bool
     */
    public function hasColumn($name): bool;

    /**
     * Get a particular column.
     * 
     * @param   string|int      $name     Column name, which could be a number.
     * @return  ColumnInterface
     * @throws  InvalidArgumentException
     */
    public function getColumn($name): ColumnInterface;

    /**
     * Get all the columns.
     * 
     * @return ColumnInterface[]
     */
    public function getColumns(): array;
}
