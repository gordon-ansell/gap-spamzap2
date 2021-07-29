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

use GreenFedora\Table\ColumnInterface;
use GreenFedora\Table\TableInterface;
use GreenFedora\Html\Html;
use GreenFedora\Filter\FilterInterface;

use GreenFedora\Table\Exception\InvalidArgumentException;

/**
 * Table column.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Column implements ColumnInterface
{
    /**
     * Column status codes.
     */
    const COL_STATUS_NONE = 0;
    const COL_STATUS_HIDDEN = 1;

    /**
     * Table.
     * @var TableInterface
     */
    protected $table;

    /**
     * Column name.
     * @var string
     */
    protected $name;

    /**
     * Title.
     * @var string
     */
    protected $title = '';

    /**
     * Header parameters.
     * @var array
     */
    protected $hdrParams = [];

    /**
     * Body parameters.
     * @var array
     */
    protected $bodyParams = [];

    /**
     * Column header class.
     * @var string
     */
    protected $hdrClass = null;

    /**
     * Column body class.
     * @var string
     */
    protected $bodyClass = null;

    /**
     * Filters.
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * Header tag.
     * @var string
     */
    protected $hdrTag = 'th';

    /**
     * Body tag.
     * @var string
     */
    protected $bodyTag = 'td';

    /**
     * Column status
     * @var int
     */
    protected $status = self::COL_STATUS_NONE;

    /**
     * Subcolumns.
     * @var ColumnInterface[]
     */
    protected $subs = [];

    /**
     * Constructor.
     * 
     * @param   TableInterface  $table          Parent table.
     * @param   string          $name           Column name.
     * @param   string          $title          Column title.
     * @param   string|null     $hdrClass       Column header class.
     * @param   string|null     $bodyClass      Column body class.
     * @param   array           $hdrParams      Header parameters.
     * @param   array           $bodyParams     Body parameters.
     * @return  void
     */
    public function __construct(TableInterface $table, string $name, string $title = '', ?string $hdrClass = null, 
        ?string $bodyClass = null, array $hdrParams = [], array $bodyParams = [])
    {
        $this->name = $name;
        $this->table = $table;
        $this->title = $title;
        $this->hdrClass = $hdrClass;
        $this->bodyClass = $bodyClass;
        $this->hdrParams = $hdrParams;
        $this->bodyParams = $bodyParams;
    }

    /**
     * Add a subcolumn.
     * 
     * @param   string          $name           Column name.
     * @param   string          $title          Column title.
     * @param   string|null     $hdrClass       Column header class.
     * @param   string|null     $bodyClass      Column body class.
     * @param   array           $hdrParams      Header parameters.
     * @param   array           $bodyParams     Body parameters.
     * @return  ColumnInterface
     */
    public function addSub(string $name, string $title = '', ?string $hdrClass = null, 
        ?string $bodyClass = null, array $hdrParams = [], array $bodyParams = []): ColumnInterface
    {
        $this->subs[$name] = new Column($this->table, $name, $title, $hdrClass, $bodyClass, $hdrParams, $bodyParams);
        return $this;
    }

    /**
     * Do we have subcolumns?
     * 
     * @return  bool
     */
    public function hasSubs(): bool 
    {
        return (count($this->subs) > 0);
    }

    /**
     * Get a subcolumn.
     * 
     * @param   string     $name      Column name.
     * @return  ColumnInterface
     * @throws  InvalidArgumentException
     */
    public function getColumn(string $name): ColumnInterface
    {
        if (array_key_exists($name, $this->subs)) {
            return $this->subs[$name];
        }
        throw new InvalidArgumentException(sprintf("No subcolumn with name '%s' found in column '%s'.", $name, $this->name));
    }

    /**
     * Get the name.
     * 
     * @var string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the title.
     * 
     * @param  string  $title   Title.
     * @return ColumnInterface 
     */
    public function setTitle(string $title): ColumnInterface
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Add a header class.
     * 
     * @param   string  $class  Class to add.
     * @return  ColumnInterface
     */
    public function addHdrClass(string $class): ColumnInterface
    {
        if (null !== $this->hdrClass and '' != $this->hdrClass) {
            $this->hdrClass .= ' ';
        }
        $this->hdrClass .= $class;
        return $this;
    }

    /**
     * Set the header class.
     * 
     * @param   string  $class  Class to set.
     * @return  ColumnInterface
     */
    public function setHdrClass(string $class): ColumnInterface
    {
        $this->hdrClass = $class;
        return $this;
    }

    /**
     * Add a body class.
     * 
     * @param   string  $class  Class to add.
     * @return  ColumnInterface
     */
    public function addBodyClass(string $class): ColumnInterface
    {
        if (null !== $this->bodyClass and '' != $this->bodyClass) {
            $this->bodyClass .= ' ';
        }
        $this->bodyClass .= $class;
        return $this;
    }

    /**
     * Set the body class.
     * 
     * @param   string  $class  Class to set.
     * @return  ColumnInterface
     */
    public function setBodyClass(string $class): ColumnInterface
    {
        $this->bodyClass = $class;
        return $this;
    }

    /**
     * Add a filter.
     * 
     * @param   FilterInterface     $filter     New filter.
     * @return  ColumnInterface 
     */
    public function addFilter(FilterInterface $filter): ColumnInterface
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * Filter the field.
     * 
     * @param   mixed   $source     Source to filter.
     * @return  mixed
     */
    public function filter($source)
    {
        if (count($this->filters) > 0) {
            foreach ($this->filters as $filter) {
                $source = $filter->filter($source);
            }
        }
        return $source;
    }

    /**
     * Set the column status.
     * 
     * @param   int     $status     Status to set.
     * @return  ColumnInterface
     */
    public function setStatus(int $status) : ColumnInterface
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set the column status hidden.
     * 
     * @param   int     $status     Status to set.
     * @return  ColumnInterface
     */
    public function setHidden() : ColumnInterface
    {
        $this->status |= self::COL_STATUS_HIDDEN;
        return $this;
    }

    /**
     * See if column is hidden.
     * 
     * @return  bool
     */
    public function isHidden(): bool
    {
        return (($this->status & self::COL_STATUS_HIDDEN) == self::COL_STATUS_HIDDEN);
    }

    /**
     * Render the header.
     * 
     * @param   string      $class      Additional classes.
     * @param   string      $tag        Tag to render.
     * @param   bool        $isBlank    Blank.
     * @return  string
     */
    public function renderHdr(?string $class = null, ?string $tag = null, bool $isBlank = false): string
    {
        if (is_null($tag)) {
            $tag = $this->hdrTag;
        }

        $params = $this->hdrParams;
        if ($this->hdrClass) {
            $params['class'] = $this->hdrClass;
        }

        if (!is_null($class)) {
            if (array_key_exists('class', $params)) {
                $params['class'] .= ' ' . $class;
            } else {
                $params['class'] = $class;
            }
        }

        $h = new Html($tag, $params);
        if ($isBlank) {
            return $h->render(' ');
        } else {
            return $h->render($this->title);
        }
    }

    /**
     * Render the body.
     * 
     * @param   mixed   $data   Data to render.
     * @param   string  $class  Additional classes.
     * @param   string  $tag    Tag to render.
     * @return  string
     */
    public function renderBody($data, ?string $class = null, ?string $tag = null): string
    {
        if (is_null($tag)) {
            $tag = $this->bodyTag;
        }

        $cls = '';
        if ($this->bodyClass) {
            $cls = $this->bodyClass;
        } else if ($this->hdrClass) {
            $cls = $this->hdrClass;
        }

        if (null !== $class) {
            if ('' != $cls and null !== $cls) {
                $cls .= ' ';
            }
            $cls .= $class;
        }

        $params = $this->bodyParams;
        $params['class'] = $cls;

        $h = new Html($tag, $params);
        return $h->render($this->filter($data));
    }
}
