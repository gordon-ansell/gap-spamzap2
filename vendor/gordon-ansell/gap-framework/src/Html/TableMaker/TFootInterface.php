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
use GreenFedora\Html\TableMaker\RowInterface;

/**
 * Table <tfoot> interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface TFootInterface extends HtmlInterface 
{
    /**
     * Set the row to span all.
     * 
     * @param   bool    $val    Value to set.
     * @return  TFootInterface
     */
    public function setSpansAll(bool $val = true): TFootInterface;

    /**
     * Get the parent table.
     * 
     * @return  TableMakerInterface
     */
    public function getTable(): TableMakerInterface;

    /**
     * Add a row.
     *
     * @param   int                                 $rownum     Row number.   
     * @param   array|PagerInterface                $data       Row data. 
     * @param   array|string                        $params     Parameters.
     * @return  TFootInterface    
     */
    public function addRow(int $rownum, $data = [], $params = []): TFootInterface;

    /**
     * Get a row.
     * 
     * @return  RowInterface
     */
    public function getRow(int $rownum): RowInterface;

    /**
     * Clear all the rows.
     * 
     * @return  TFootInterface
     */
    public function clearAllRows(): TFootInterface;

}
