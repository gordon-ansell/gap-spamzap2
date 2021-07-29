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
use GreenFedora\Html\TableMaker\THeadInterface;
use GreenFedora\Html\TableMaker\ColumnInterface;
use GreenFedora\Filter\Slugify;

/**
 * Table column.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Column extends Html implements ColumnInterface
{
    /**
     * Column title.
     * @var string
     */
    protected $title = null;

    /**
     * Column name.
     * @var string
     */
    protected $name = null;

    /**
     * Head parent.
     * @var THeadInterface
     */
    protected $thead = null;

    /**
     * Constructor.
     * 
     * @param   THeadInterface  $thead      Parent thead.
     * @param   string          $title      Column title.
     * @param   string          $name       Column name.
     * @param   array|string    $params     Parameters.
     * @return  ColumnInterface    
     */
    public function __construct(THeadInterface $thead = null, string $title, string $name = null, $params = [])
    {
        $this->thead = $thead;
        $this->title = $title;

        if (!is_null($name)) {
            $this->name = $name;
        } else {
            $sf = new Slugify();
            $this->name = $sf->filter($title);
        }

        if (is_string($params)) {
            $params = ['class' => $params];
        }

        if (array_key_exists('class', $params) and !empty($params['class'])) {
            $params['class'] .= ' col-' . $this->name;
        } else {
            $params['class'] = 'col-' . $this->name;
        }

        parent::__construct('td', $params);
    }

    /**
     * Get the head.
     * 
     * @return  THeadInterface
     */
    public function getHead(): THeadInterface
    {
        return $this->thead;
    }

    /**
     * Render the column for the head.
     * 
     * @return  string
     */
    public function renderHead(): string
    {
        $this->setTag('th');
        return $this->render($this->title);
    }

    /**
     * Render the column for the body.
     * 
     * @param   string      $data   Data to render.
     * @return  string
     */
    public function renderBody(string $data): string
    {
        $this->setTag('td');
        return $this->render($data);
    }
}
