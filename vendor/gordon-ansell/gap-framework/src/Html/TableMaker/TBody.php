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

use GreenFedora\Html\Html;
use GreenFedora\Html\TableMaker\TableMakerInterface;
use GreenFedora\Html\TableMaker\TBodyInterface;
use GreenFedora\Html\TableMaker\Row;
use GreenFedora\Html\TableMaker\RowInterface;
use GreenFedora\Html\TableMaker\Exception\InvalidArgumentException;
use GreenFedora\Html\TableMaker\Exception\RuntimeException;

/**
 * Table <tbody>.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class TBody extends Html implements TBodyInterface
{
    /**
     * Parent table.
     * @var TableMakerInterface
     */
    protected $table = null;

    /**
     * Table rows.
     * @var RowInterface[]
     */
    protected $rows = [];

    /**
     * Convert null data to blank?
     * @var bool
     */
    protected $nullToBlank = true;

    /**
     * Constructor.
     * 
     * @param   TableInterface  $table      Parent table.
     * @param   array           $params     Parameters.
     * @return  TBodyInterface    
     */
    public function __construct(TableMakerInterface $table = null, array $params = [])
    {
        $this->table = $table;
        parent::__construct('tbody', $params);
    }

    /**
     * Get the parent table.
     * 
     * @return  TableMakerInterface
     */
    public function getTable(): TableMakerInterface
    {
        return $this->table;
    }

    /**
     * Add a row.
     *
     * @param   int                                 $rownum     Row number.   
     * @param   array                               $data       Row data. 
     * @param   array|string                        $params     Parameters.
     * @return  TBodyInterface    
     */
    public function addRow(int $rownum, $data = [], $params = []): TBodyInterface
    {
        if (array_key_exists($rownum, $this->rows)) {
            throw new InvalidArgumentException(sprintf("A row numbered '%s' already exists on this table.", $rownum));            
        }

        $this->rows[$rownum] = new Row($this, $rownum, $data, $params);

        return $this;
    }

    /**
     * Get a row.
     * 
     * @return  RowInterface
     */
    public function getRow(int $rownum): RowInterface
    {
        if (!array_key_exists($rownum, $this->rows)) {
            throw new InvalidArgumentException(sprintf("No row numbered '%s' exists on this table.", $rownum));            
        }
        return $this->rows[$rownum];
    }

    /**
     * Clear all the rows.
     * 
     * @return  TBodyInterface
     */
    public function clearAllRows(): TBodyInterface
    {
        $this->rows = [];
        return $this;
    }

	/**
	 * Render the body (normal).
	 *
	 * @param 	string|null	$data 	        Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function render(?string $data = null, ?array $extraParams = null) : string
    {
        $data = '';
        foreach($this->rows as $row) {
            $rowData = '';
            foreach($this->getTable()->thead()->getColumns() as $k => $col) {
                $rowData .= $col->renderBody($row->getColumnData($k));
            }
            $data .= $row->render($rowData);
        }
        return parent::render($data, $extraParams);
    }

	/**
	 * Render the body (vertical).
	 *
	 * @param 	string|null	$data 	        Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function renderVertical(?string $data = null, ?array $extraParams = null) : string
    {
        $data = '';
        foreach($this->rows as $r => $row) {
            foreach($this->getTable()->thead()->getColumns() as $k => $col) {
                $colData = $row->getColumnData($k);
                if (is_null($colData)) {
                    if ($this->nullToBlank) {
                        $colData = '';
                    } else {
                        throw new RuntimeException(sprintf("Null data encountered. Column '%s', row %s.", $k, $r));
                    }
                }
                $data .= $row->render($col->renderHead() . $col->renderBody($row->getColumnData($k)));
            }
        }
        return parent::render($data, $extraParams);
    }
}
