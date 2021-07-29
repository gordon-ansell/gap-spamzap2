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
use GreenFedora\Html\TableMaker\TBodyInterface;
use GreenFedora\Html\TableMaker\TableMakerInterface;

/**
 * Table row interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface RowInterface extends HtmlInterface 
{    
    /**
     * Add a subrow.
     *
     * @param   int                                 $subrownum  Subrow number.   
     * @param   TableMakerInterface                 $data       Row data. 
     * @param   array|string                        $params     Parameters.
     * @return  RowInterface    
     */
    public function addSubRow(int $subrownum, TableMakerInterface $data, $params = []): RowInterface;

    /**
     * Get the row number.
     * 
     * @return int
     */
    public function getRowNum(): int;

    /**
     * Get the parent body.
     * 
     * @return TBodyInterface
     */
    public function getBody(): TBodyInterface;

    /**
     * Get the data.
     * 
     * @return  array
     */
    public function getData(): array;

    /**
     * Get the data for a column.
     * 
     * @param   string|int  $name   Column to get data for.
     * @return  mixed
     */
    public function getColumnData($name);
}
