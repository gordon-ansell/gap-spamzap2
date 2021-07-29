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
use GreenFedora\Table\ColumnInterface;
use GreenFedora\Table\Column;
use GreenFedora\Html\Html;
use GreenFedora\Stdlib\Arr\Arr;

use GreenFedora\Table\Exception\InvalidArgumentException;

/**
 * Subrow interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface SubRowInterface
{
    /**
     * Get the name.
     * 
     * @return  string
     */
    public function getName(): string;

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
        ?string $bodyClass = null, array $hdrParams = [], array $bodyParams = []): SubRowInterface;

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
     * @return  SubrowInterface
     */
    public function addClass(string $class): SubRowInterface;

    /**
     * Set the class.
     * 
     * @param   string  $class  Class to set.
     * @return  SubRowInterface
     */
    public function setClass(string $class): SubRowInterface;

    /**
     * Render the subrow.
     * 
     * @param   iterable    $row    Row data.
     * @param   int         $rownum Row number.
     * @return  string
     */
    public function render(iterable $row, int $rownum): string;

}
