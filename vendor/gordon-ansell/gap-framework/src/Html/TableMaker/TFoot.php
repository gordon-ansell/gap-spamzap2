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
use GreenFedora\Html\TableMaker\TFootInterface;
use GreenFedora\Html\TableMaker\Row;
use GreenFedora\Html\TableMaker\RowInterface;
use GreenFedora\Html\TableMaker\Exception\InvalidArgumentException;
use GreenFedora\Html\TableMaker\Exception\RuntimeException;

/**
 * Table <tfoot>.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class TFoot extends Html implements TFootInterface
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
     * Spans all?
     * @var bool
     */
    protected $spansAll = false;

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
        parent::__construct('tfoot', $params);
    }

    /**
     * Set the row to span all.
     * 
     * @param   bool    $val    Value to set.
     * @return  TFootInterface
     */
    public function setSpansAll(bool $val = true): TFootInterface
    {
        $this->spansAll = $val;
        return $this;
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
     * @param   array|PagerInterface                $data       Row data. 
     * @param   array|string                        $params     Parameters.
     * @return  TFootInterface    
     */
    public function addRow(int $rownum, $data = [], $params = []): TFootInterface
    {
        if (array_key_exists($rownum, $this->rows)) {
            throw new InvalidArgumentException(sprintf("A row numbered '%s' already exists on this table (footer).", $rownum));            
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
            throw new InvalidArgumentException(sprintf("No row numbered '%s' exists on this table (footer).", $rownum));            
        }
        return $this->rows[$rownum];
    }

    /**
     * Clear all the rows.
     * 
     * @return  TFootInterface
     */
    public function clearAllRows(): TFootInterface
    {
        $this->rows = [];
        return $this;
    }

	/**
	 * Render the foot.
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
            if ($this->spansAll) {
                $fcData = $row->getFirstColumnData();
                if ($fcData instanceof PagerInterface) {
                    $rowData = '<td colspan="999" class="spanall">' . $fcData->render() . '</td>';
                } else {
                    $rowData = '<td colspan="999" class="spanall">' . $fcData . '</td>';
                }
            } else {
                foreach($this->getTable()->thead()->getColumns() as $k => $col) {
                    $rowData .= $col->renderBody($row->getColumnData($k));
                }
            }
            $data .= $row->render($rowData);
        }
        return parent::render($data, $extraParams);
    }

}
