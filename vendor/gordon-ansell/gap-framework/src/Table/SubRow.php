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
namespace GreenFedora\Table;

use GreenFedora\Table\TableInterface;
use GreenFedora\Table\SubRowInterface;
use GreenFedora\Table\ColumnInterface;
use GreenFedora\Table\Column;
use GreenFedora\Html\Html;
use GreenFedora\Stdlib\Arr\Arr;

use GreenFedora\Table\Exception\InvalidArgumentException;

/**
 * Subrow.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class SubRow implements SubRowInterface
{
    /**
     * Parent table.
     * @var TableInterface
     */
    protected $table = null;

    /**
     * Name.
     * @var string
     */
    protected $name = null;

    /**
     * Parameters.
     * @var array
     */
    protected $params = [];

    /**
     * Class.
     * @var string
     */
    protected $class = null;

    /**
     * Columns.
     * @var array
     */
    protected $columns = [];

    /**
     * Row tag.
     * @var string
     */
    protected $rowTag = 'tr';

    /**
     * Header tag.
     * @var string
     */
    protected $headTag = 'th';

    /**
     * Body tag.
     * @var string
     */
    protected $bodyTag = 'td';

    /**
     * Constructor.
     * 
     * @param   TableInterface          $table              Parent.
     * @param   string                  $name               Subrow name.
     * @param   string|null             $class              Class.
     * @param   array                   $params             Parameters.
     * @return  void
     */
    public function __construct(TableInterface $table, string $name, ?string $class = null, array $params = [])
    {
        $this->table = $table;
        $this->name = $name;
        $this->class = $class;
        $this->params = $params;
    }

    /**
     * Get the name.
     * 
     * @return  string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Add a column.
     * 
     * @param   string|ColumnInterface      $name           Column name or instance.
     * @param   string                      $title          Column title.
     * @param   string|null                 $hdrClass       Column header class.
     * @param   string|null                 $bodyClass      Column body class.
     * @param   array                       $hdrParams      Header parameters.
     * @param   array                       $bodyParams     Body parameters.
     * @return  TableInterface
     */
    public function addColumn($name, string $title = '', ?string $hdrClass = null, 
        ?string $bodyClass = null, array $hdrParams = [], array $bodyParams = []): SubRowInterface
    {
        if ($name instanceof ColumnInterface) {
            $this->columns[$name->getName()] = $name;
            return $this;
        }

        $this->columns[$name] = new Column($this->table, $name, $title, $hdrClass, $bodyClass, $hdrParams, $bodyParams);
        return $this;
    }

    /**
     * Get a column.
     * 
     * @param   string     $name      Column name.
     * @return  ColumnInterface
     * @throws  InvalidArgumentException
     */
    public function getColumn(string $name): ColumnInterface
    {
        if (array_key_exists($name, $this->columns)) {
            return $this->columns[$name];
        }
        throw new InvalidArgumentException(sprintf("No column with name '%s' found.", $name));
    }

    /**
     * Get all the columns.
     * 
     * @return  ColumnInterface[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Add a class.
     * 
     * @param   string  $class  Class to add.
     * @return  SubRowInterface
     */
    public function addClass(string $class): SubRowInterface
    {
        if (null !== $this->class and '' != $this->class) {
            $this->class .= ' ';
        }
        $this->class .= $class;
        return $this;
    }

    /**
     * Set the class.
     * 
     * @param   string  $class  Class to set.
     * @return  SubRowInterface
     */
    public function setClass(string $class): SubRowInterface
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Render the head.
     * 
     * @return string
     */
    /*
    public function renderHdr()
    {
        $thead = new Html($this->headTag);
        $tr = new Html($this->rowTag);

        $ret = '';

        foreach($this->columns as $k => $v) {
            if (!$v->isHidden()) {
                $ret .= $v->renderHdr();
            }
        }

        if ($this->hasSortableColumns) {
            $fparams = array(
                'name'      =>  $this->name . '-form',
                'class'     =>  'tableform tableform-' . $this->name,
                'method'    =>  'POST'
            );
            $f = new Html('form', $fparams);
            $ret = $f->render("<input type='hidden' name='form-submitted' value='" . $this->name . "' />" . $ret);
        }

        return $thead->render($tr->render($ret));
    }
    */

    /**
     * Render the body.
     * 
     * @return string
     */
    /*
    public function renderBody()
    {
        $tbody = new Html($this->bodyTag);
        $tr = new Html($this->rowTag);

        $ret = '';

        foreach ($this->data as $row) {
            $trData = '';
            $rowData = null;
            if (is_object($row)) {
                $rowData = $row->toArray();
            } else {
                $rowData = $row;
            }

            if (!Arr::isArraySequential($rowData)) {
                foreach($this->columns as $k => $v) {
                    if (!$v->isHidden()) {

                        $trData .= $v->renderHdr();

                        if (is_array($rowData[$k])) {
                            $trData .= $v->renderBody(strval(array_values($rowData[$k])[0]));
                        } else {
                            $trData .= $v->renderBody(strval($rowData[$k]));
                        }
                    }
                }
            } else {

                $count = 0;
                foreach($this->columns as $k => $v) {
                    if (!$v->isHidden()) {

                        $trData .= $v->renderHdr();

                        if (is_array($rowData[$count])) {
                            $trData .= $v->renderBody(strval(array_values($rowData[$count])[0]));
                        } else {
                            $trData .= $v->renderBody(strval($rowData[$count]));
                        }
                    }
                    $count++;
                }
            }
            $ret .= $tr->render($trData);
        }

        return $tbody->render($ret);
    }
    */

    /**
     * Render the subrow.
     * 
     * @param   iterable    $row    Row data.
     * @param   int         $rownum Row number.
     * @return  string
     */
    public function render(iterable $row, int $rownum): string
    {
        $params = $this->params;
        if ($this->class) {
            $params['class'] = $this->class;
        }
        $params['id'] = 'subrow-' . $rownum;
        $subrow = new Html($this->rowTag, $params);

        $rowCols = '';
        $trData = '';
        $rowData = null;

        if (is_object($row)) {
            $rowData = $row->toArray();
        } else {
            $rowData = $row;
        }

        if (!Arr::isArraySequential($rowData)) {
            foreach($this->columns as $k => $v) {
                if (!$v->isHidden()) {

                    if ('blank' == substr($k, 0, strlen('blank'))) {
                        $trData .= $v->renderHdr('subrow-hdr', 'span', true);
                        $trData .= $v->renderBody(' ', 'subrow-body', 'span');
                    } else {
                    
                        $trData .= $v->renderHdr('subrow-hdr', 'span');

                        if (is_array($rowData[$k])) {
                            $trData .= $v->renderBody(strval(array_values($rowData[$k])[0]), 'subrow-body', 'span');
                        } else {
                            $trData .= $v->renderBody(strval($rowData[$k]), 'subrow-body', 'span');
                        }
                    }
                }

                $trData = '<div class="subrow-container">' . $trData . '</div>';
            }
        } else {

            $count = 0;
            foreach($this->columns as $k => $v) {
                if (!$v->isHidden()) {

                    $trData .= $v->renderHdr('subrow-hdr', 'span');

                    if (is_array($rowData[$count])) {
                        $trData .= $v->renderBody(strval(array_values($rowData[$count])[0]), 'subrow-body', 'span');
                    } else {
                        $trData .= $v->renderBody(strval($rowData[$count]), 'subrow-body', 'span');
                    }
                }
                $count++;
            }
            $trData = '<div class="subrow-container">' . $trData . '</div>';
        }

        $rowCols .= $trData;

        $rowCols = '<td class="subrow-column">' . $rowCols . '</td>';

        $ret = $subrow->render($rowCols);

        return $ret;
    }

}
