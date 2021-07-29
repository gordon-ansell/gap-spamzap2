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

use GreenFedora\Session\SessionInterface;
use GreenFedora\Http\HttpRequestInterface;
use GreenFedora\Table\ColumnInterface;
use GreenFedora\Table\SubRowInterface;

/**
 * Table maker interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface TableInterface
{
    /**
     * Check the sorting.
     * 
     * @param   HttpRequestInterface    $request    Request object.
     * @param   SessionInterface        $session    Session object.
     * @return  TableInterface
     */
    public function checkSort(HttpRequestInterface $request, ?SessionInterface $session): TableInterface;

    /**
     * Get the name.
     * 
     * @return  string
     */
    public function getName(): string;

    /**
     * Set the next row class.
     * 
     * @param   int     $num    Row number.
     * @param   string  $class  Class to set.
     * 
     * @return  TableInterface
     */
    public function setRowClass(int $row, string $class): TableInterface;

    /**
     * Add a subrow.
     * 
     * @param   string                      $name           Name.
     * @param   string|null                 $class          Class.
     * @param   array                       $params         Params.
     * @return  TableInterface
     */
    public function addSubRow($name, ?string $class = null, array $params = []): TableInterface;

    /**
     * Get a subrow.
     * 
     * @param   string     $name      Column name.
     * @return  SubRowInterface
     * @throws  InvalidArgumentException
     */
    public function getSubRow(string $name): SubRowInterface;
 
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
        ?string $bodyClass = null, array $hdrParams = [], array $bodyParams = []): TableInterface;

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
        ?string $bodyClass = null, array $hdrParams = [], array $bodyParams = []): TableInterface;

    /**
     * Set the sort column.
     * 
     * @param   string|null $col    Column.
     * @param   string      $dir    Direction.
     * @return  TableInterface
     */
    public function setSort(?string $col, string $dir = 'asc'): TableInterface;

    /**
     * Get the sort column.
     * 
     * @return  array|null
     */
    public function getSort(): ?array;

    /**
     * Get a column.
     * 
     * @param   string     $name      Column name.
     * @return  ColumnInterface
     * @throws  InvalidArgumentException
     */
    public function getColumn(string $name): ColumnInterface;

    /**
     * Get all the columns.
     * 
     * @return  ColumnInterface[]
     */
    public function getColumns(): array;

    /**
     * Add a class.
     * 
     * @param   string  $class  Class to add.
     * @return  TableInterface
     */
    public function addClass(string $class): TableInterface;

    /**
     * Set the class.
     * 
     * @param   string  $class  Class to set.
     * @return  TableInterface
     */
    public function setClass(string $class): TableInterface;

    /**
     * Set the data.
     * 
     * @param   iterable        $data   Data to set.
     * @return  TableInterface
     */
    public function setData(iterable $data): TableInterface;

    /**
     * Get the data.
     * 
     * @return  iterable
     */
    public function getData(): iterable;

    /**
     * Render the head.
     * 
     * @return string
     */
    public function renderHdr();

    /**
     * Render the body.
     * 
     * @return string
     */
    public function renderBody();

    /**
     * Render the table.
     * 
     * @return  string
     */
    public function render(): string;
}
