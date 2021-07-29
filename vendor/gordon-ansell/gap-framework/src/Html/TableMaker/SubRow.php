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
use GreenFedora\Html\TableMaker\TBodyInterface;
use GreenFedora\Html\TableMaker\RowInterface;
use GreenFedora\Html\TableMaker\SubRowInterface;

/**
 * Table subrow.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class SubRow extends Html implements SubRowInterface
{
    /**
     * Parent row.
     * @var RowInterface
     */
    protected $row = null;

    /**
     * Sub row number.
     * @var int
     */
    protected $subrownum = null;

    /**
     * Row data.
     * @var TableMakerInterface
     */
    protected $rowData = null;

    /**
     * Constructor.
     *
     * @param   RowInterface                    $row        Parent row.
     * @param   int                             $subrownum  Row number.   
     * @param   TableMakerInterface             $rowData    Row data. 
     * @param   array|string                    $params     Parameters.
     * @return  SubRowInterface    
     */
    public function __construct(RowInterface $row, int $subrownum, TableMakerInterface $rowData, $params = [])
    {
        $this->row = $row;
        $this->subrownum = $subrownum;
        $this->rowData = $rowData;

        $prn = $this->row->getRowNum();

        if (is_string($params)) {
            $params = ['class' => $params];
        }

        if (array_key_exists('class', $params) and !empty($params['class'])) {
            $params['class'] .= ' subrow row-' . strval($prn) . '-' . strval($this->subrownum);
        } else {
            $params['class'] = 'subrow row-' . strval($prn) . '-' . strval($this->subrownum);
        }
        $params['id'] = 'subrow-' . strval($prn) . '-' . strval($this->subrownum);

        parent::__construct('tr', $params);
    }

    /**
     * Get the parent row.
     * 
     * @return TBodyInterface
     */
    public function getRow(): RowInterface
    {
        return $this->row;
    }

    /**
     * Get the data.
     * 
     * @return  TableMakerInterface
     */
    public function getData(): TableMakerInterface
    {
        return $this->rowData;
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
        $data = '<td colspan="100" class="subrowcell">' . $this->rowData->render() . '</td>';
        return parent::render($data, $extraParams);
    }
}
