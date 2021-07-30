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
use GreenFedora\Html\TableMaker\Exception\InvalidArgumentException;
use GreenFedora\Html\TableMaker\TBodyInterface;
use GreenFedora\Html\TableMaker\RowInterface;

/**
 * Table row.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Row extends Html implements RowInterface
{
    /**
     * Body parent.
     * @var TBodyInterface
     */
    protected $tbody = null;

    /**
     * Row number.
     * @var int
     */
    protected $rownum = null;

    /**
     * Row data.
     * @var array|TableMakerInterface|PagerInterface
     */
    protected $rowData = [];

    /**
     * Subrows.
     * @var SubRowInterface[]
     */
    protected $subrows = [];

    /**
     * Constructor.
     *
     * @param   TBodyInterface|TFootInterface   $tbody      Parent body or foot.
     * @param   int                             $rownum     Row number.   
     * @param   array|PagerInterface            $rowdata    Row data. 
     * @param   array|string                    $params     Parameters.
     * @return  RowInterface    
     */
    public function __construct($tbody, int $rownum, $rowData = [], $params = [])
    {
        $this->tbody = $tbody;
        $this->rownum = $rownum;
        $this->rowData = $rowData;

        if (is_string($params)) {
            $params = ['class' => $params];
        }

        if (array_key_exists('class', $params) and !empty($params['class'])) {
            $params['class'] .= ' row-' . strval($this->rownum);
        } else {
            $params['class'] = 'row-' . strval($this->rownum);
        }

        parent::__construct('tr', $params);
    }

    /**
     * Add a subrow.
     *
     * @param   int                                 $subrownum  Subrow number.   
     * @param   TableMakerInterface                 $data       Row data. 
     * @param   array|string                        $params     Parameters.
     * @return  RowInterface    
     */
    public function addSubRow(int $subrownum, TableMakerInterface $data, $params = []): RowInterface
    {
        if (array_key_exists($subrownum, $this->subrows)) {
            throw new InvalidArgumentException(sprintf("A subrow numbered '%s' already exists on row %s.", 
                $subrownum, $this->rownum));            
        }

        $this->subrows[$subrownum] = new SubRow($this, $subrownum, $data, $params);

        return $this;
    }

    /**
     * Get the row number.
     * 
     * @return int
     */
    public function getRowNum(): int
    {
        return $this->rownum;
    }

    /**
     * Get the parent body or foot.
     * 
     * @return TBodyInterface|TFootInterface
     */
    public function getBody()
    {
        return $this->tbody;
    }

    /**
     * Get the parent body or foot.
     * 
     * @return TBodyInterface|TFootInterface
     */
    public function getFoot()
    {
        return $this->tbody;
    }

    /**
     * Get the data.
     * 
     * @return  array
     */
    public function getData(): array
    {
        return $this->rowData;
    }

    /**
     * Get the data for a column.
     * 
     * @param   string|int  $name   Column to get data for.
     * @return  mixed
     */
    public function getColumnData($name)
    {
        if (!array_key_exists($name, $this->rowData)) {
            throw new InvalidArgumentException(sprintf("Row data does not contain a column '%s' (row: %s).", 
                $name, $this->rownum));
        }
        return $this->rowData[$name];
    }

    /**
     * Get the data for the first column.
     * 
     * @return  mixed
     */
    public function getFirstColumnData()
    {
        return $this->rowData[0];
    }

    /**
	 * Render the row.
	 *
	 * @param 	string|null	$data 	        Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function render(?string $data = null, ?array $extraParams = null) : string
    {
        $ret = parent::render($data, $extraParams);

        if (count($this->subrows) > 0) {
            foreach ($this->subrows as $sr) {
                $ret .= $sr->render();
            }
        }

        return $ret;
    }
}
