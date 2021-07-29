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
use GreenFedora\Html\TableMaker\THeadInterface;
use GreenFedora\Html\TableMaker\Column;
use GreenFedora\Html\TableMaker\ColumnInterface;
use GreenFedora\Filter\Slugify;
use GreenFedora\Html\TableMaker\Exception\InvalidArgumentException;

/**
 * Table <thead>.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class THead extends Html implements THeadInterface
{
    /**
     * Parent table.
     * @var TableMakerInterface
     */
    protected $table = null;

    /**
     * Columns.
     * @var ColumnInterface[]
     */
    protected $columns = [];

    /**
     * Constructor.
     * 
     * @param   TableMakerInterface $table      Parent table.
     * @param   array               $params     Parameters.
     * @return  THeadInterface    
     */
    public function __construct(TableMakerInterface $table = null, array $params = [])
    {
        $this->table = $table;
        parent::__construct('thead', $params);
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
     * Add a column.
     * 
     * @param   string          $title      Column title.
     * @param   string          $name       Column name.
     * @param   array|string    $params     Parameters.
     * @return  THeadInterface    
     * @throws  InvalidArgumentException
     */
    public function addColumn(string $title, string $name = null, $params = []): THeadInterface
    {
        if (is_null($name)) {
            $sf = new Slugify();
            $name = $sf->filter($title);
        }

        if (array_key_exists($name, $this->columns)) {
            throw new InvalidArgumentException(sprintf("A column named '%s' already exists on this table.", $name));            
        }

        $this->columns[$name] = new Column($this, $title, $name, $params);

        return $this;
    }

    /**
     * See if we have a particular column.
     * 
     * @param   string|int      $name     Column name, which could be a number.
     * @return  bool
     */
    public function hasColumn($name): bool
    {
        return array_key_exists($name, $this->columns);
    }

    /**
     * Get a particular column.
     * 
     * @param   string|int      $name     Column name, which could be a number.
     * @return  ColumnInterface
     * @throws  InvalidArgumentException
     */
    public function getColumn($name): ColumnInterface
    {
        if (!array_key_exists($name, $this->columns)) {
            throw new InvalidArgumentException(sprintf("A column named '%s' could not be found.", $name));            
        }

        return $this->columns[$name];
    }

    /**
     * Get all the columns.
     * 
     * @return ColumnInterface[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

	/**
	 * Render the head.
	 *
	 * @param 	string|null	$data 	        Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function render(?string $data = null, ?array $extraParams = null) : string
    {
        $data = '';
        foreach($this->columns as $col) {
            $data .= $col->renderHead();
        }

        return parent::render('<tr class="header">' . $data . '</tr>', $extraParams);
    }
}
