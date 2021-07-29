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
 * Table <tbody> interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface TBodyInterface extends HtmlInterface 
{
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
     * @param   array|TableMakerInterface           $data       Row data. 
     * @param   array|string                        $params     Parameters.
     * @return  TBodyInterface    
     */
    public function addRow(int $rownum, $data = [], $params = []): TBodyInterface;

    /**
     * Get a row.
     * 
     * @return  RowInterface
     */
    public function getRow(int $rownum): RowInterface;

    /**
     * Clear all the rows.
     * 
     * @return  TBodyInterface
     */
    public function clearAllRows(): TBodyInterface;

    /**
	 * Render the body (vertical).
	 *
	 * @param 	string|null	$data 	        Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function renderVertical(?string $data = null, ?array $extraParams = null) : string;
}
