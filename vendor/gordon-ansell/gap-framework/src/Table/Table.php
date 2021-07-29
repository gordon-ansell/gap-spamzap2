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
use GreenFedora\Session\SessionInterface;
use GreenFedora\Http\HttpRequestInterface;
use GreenFedora\Stdlib\Arr\Arr;

use GreenFedora\Table\Exception\InvalidArgumentException;

/**
 * Table maker.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Table implements TableInterface
{

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
     * @var ColumnInterface[]
     */
    protected $columns = [];

    /**
     * Data.
     * @var iterable
     */
    protected $data = [];

    /**
     * Table tag.
     * @var string
     */
    protected $tableTag = 'table';

    /**
     * Header tag.
     * @var string
     */
    protected $headTag = 'thead';

    /**
     * Body tag.
     * @var string
     */
    protected $bodyTag = 'tbody';

    /**
     * Row tag.
     * @var string
     */
    protected $rowTag = 'tr';

    /**
     * Do we have sortable columns?
     * @var bool
     */
    protected $hasSortableColumns = false;

    /**
     * Sort order.
     * @var array|null
     */
    protected $sortCol = null;

    /**
     * Subrows.
     * @var SubRowInterface[]
     */
    protected $subrows = [];

    /**
     * Row classes.
     * @var array
     */
    protected $rowClasses = [];

    /**
     * Constructor.
     * 
     * @param   string                  $name               Table name.
     * @param   string|null             $class              Class.
     * @param   array                   $params             Parameters.
     * @return  void
     */
    public function __construct(string $name, ?string $class = null, array $params = [])
    {
        $this->name = $name;
        $this->class = $class;
        $this->params = $params;
    }

    /**
     * Check the sorting.
     * 
     * @param   HttpRequestInterface    $request    Request object.
     * @param   SessionInterface        $session    Session object.
     * @return  TableInterface
     */
    public function checkSort(HttpRequestInterface $request, ?SessionInterface $session): TableInterface
    {
        $sortcol = null;
        $sortdir = null;
        if ($request->formSubmitted($this->name)) {
            $sortcol = $request->post('sortcol', null);
            $sortdir = $request->post('sortdir', null);
        }
        if ('off' == $sortdir) {
            $session->unset($this->name . '-sortcol');
            $session->unset($this->name . '-sortdir');
            $this->setSort(null);
            return $this;
        }
        if (null === $sortcol) {
            $sortcol = $session->get($this->name . '-sortcol', null);
        }
        if (null === $sortdir) {
            $sortdir = $session->get($this->name . '-sortdir', null);
        }

        if (null === $sortcol) {
            $this->setSort(null);
        } else {
            if (null === $sortdir) {
                $sortdir = 'asc';
            } 
            $this->setSort($sortcol, $sortdir);
            $session->set($this->name . '-sortcol', $sortcol);
            $session->set($this->name . '-sortdir', $sortdir);
        }

        return $this;
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
     * Set the next row class.
     * 
     * @param   int     $num    Row number.
     * @param   string  $class  Class to set.
     * 
     * @return  TableInterface
     */
    public function setRowClass(int $row, string $class): TableInterface
    {
        $this->rowClasses['row-' . strval($row)] = $class;
        return $this;
    }

    /**
     * Add a subrow.
     * 
     * @param   string                      $name           Name.
     * @param   string|null                 $class          Class.
     * @param   array                       $params         Params.
     * @return  TableInterface
     */
    public function addSubRow($name, ?string $class = null, array $params = []): TableInterface
    {
        $this->subrows[$name] = new SubRow($this, $name, $class, $params);
        return $this;
    }

    /**
     * Get a subrow.
     * 
     * @param   string     $name      Column name.
     * @return  SubRowInterface
     * @throws  InvalidArgumentException
     */
    public function getSubRow(string $name): SubRowInterface
    {
        if (array_key_exists($name, $this->subrows)) {
            return $this->subrows[$name];
        }
        throw new InvalidArgumentException(sprintf("No subrow with name '%s' found.", $name));
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
        ?string $bodyClass = null, array $hdrParams = [], array $bodyParams = []): TableInterface
    {
        if ($name instanceof ColumnInterface) {
            $this->columns[$name->getName()] = $name;
            return $this;
        }

        $this->columns[$name] = new Column($this, $name, $title, $hdrClass, $bodyClass, $hdrParams, $bodyParams);
        return $this;
    }

    /**
     * Add a sortable column.
     * 
     * @param   string|SortableColumnInterface  $name           Column name or instance.
     * @param   string                          $title          Column title.
     * @param   string|null                     $hdrClass       Column header class.
     * @param   string|null                     $bodyClass      Column body class.
     * @param   array                           $hdrParams      Header parameters.
     * @param   array                           $bodyParams     Body parameters.
     * @return  TableInterface
     */
    public function addSortableColumn($name, string $title = '', ?string $hdrClass = null, 
        ?string $bodyClass = null, array $hdrParams = [], array $bodyParams = []): TableInterface
    {
        if ($name instanceof SortableColumnInterface) {
            $this->columns[$name->getName()] = $name;
            $this->hasSortableColumns = true;
            return $this;
        }

        $this->columns[$name] = new SortableColumn($this, $name, $title, $hdrClass, $bodyClass, $hdrParams, $bodyParams);
        $this->hasSortableColumns = true;
        return $this;
    }

    /**
     * Set the sort column.
     * 
     * @param   string|null $col    Column.
     * @param   string      $dir    Direction.
     * @return  TableInterface
     */
    public function setSort(?string $col, string $dir = 'asc'): TableInterface
    {
        if (null === $col) {
            $this->sortCol = null;
        } else {
            $this->sortCol = array($col, $dir);
        }
        return $this;
    }

    /**
     * Get the sort column.
     * 
     * @return  array|null
     */
    public function getSort(): ?array
    {
        return $this->sortCol;
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
     * @return  TableInterface
     */
    public function addClass(string $class): TableInterface
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
     * @return  TableInterface
     */
    public function setClass(string $class): TableInterface
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Set the data.
     * 
     * @param   iterable        $data   Data to set.
     * @return  TableInterface
     */
    public function setData(iterable $data): TableInterface
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get the data.
     * 
     * @return  iterable
     */
    public function getData(): iterable
    {
        return $this->data;
    }

    /**
     * Render the head.
     * 
     * @return string
     */
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

    /**
     * Render the body.
     * 
     * @return string
     */
    public function renderBody()
    {
        $tbody = new Html($this->bodyTag);
        $tr = new Html($this->rowTag);
        if (count($this->subrows) > 0) {
            $tr->appendParam('class', 'with-subrows');
        }

        $ret = '';

        $rownum = 1;

        foreach ($this->data as $row) {
            $trData = '';
            $rowData = null;
            if (is_object($row)) {
                $rowData = $row->toArray();
            } else {
                $rowData = $row;
            }

            if (!Arr::isArraySequential($rowData)) {
                $colnum = 1;
                foreach($this->columns as $k => $v) {
                    if (!$v->isHidden()) {
                        if (is_array($rowData[$k])) {
                            $trData .= $v->renderBody(strval(array_values($rowData[$k])[0]), 'col-' . $colnum);
                        } else {
                            $trData .= $v->renderBody(strval($rowData[$k]), 'col-' . $colnum);
                        }
                    }
                    $colnum++;
                }
            } else {

                $count = 0;
                $colnum = 1;
                foreach($this->columns as $k => $v) {
                    if (!$v->isHidden()) {
                        if (is_array($rowData[$count])) {
                            $trData .= $v->renderBody(strval(array_values($rowData[$count])[0]), 'col-' . $colnum);
                        } else {
                            $trData .= $v->renderBody(strval($rowData[$count]), 'col-' . $colnum);
                        }
                    }
                    $count++;
                    $colnum++;
                }
            }

            $rc = 'row-' . $rownum;
            if (array_key_exists('row-' . strval($rownum), $this->rowClasses)) {
                $rc .= ' ' . $this->rowClasses['row-' . strval($rownum)];
            }
            $ret .= $tr->render($trData, ['class' => $rc]);

            if (count($this->subrows) > 0) {
                foreach ($this->subrows as $sr) {
                    $ret .= $sr->render($row, $rownum);
                }
            }

            $rownum++;
        }


        return $tbody->render($ret);
    }

    /**
     * Render the table.
     * 
     * @return  string
     */
    public function render(): string
    {
        $params = $this->params;
        if ($this->class) {
            $params['class'] = $this->class;
        }
        $table = new Html($this->tableTag, $params);

        $ret = $table->render($this->renderHdr() . $this->renderBody());

        return $ret;
    }

}
