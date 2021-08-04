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
use GreenFedora\Html\TableMaker\Exception\RuntimeException;
use GreenFedora\Html\TableMaker\TableMakerInterface;
use GreenFedora\Html\TableMaker\THeadInterface;
use GreenFedora\Html\TableMaker\THead;
use GreenFedora\Html\TableMaker\TBodyInterface;
use GreenFedora\Html\TableMaker\TBody;
use GreenFedora\Html\TableMaker\TFootInterface;
use GreenFedora\Html\TableMaker\TFoot;

/**
 * Table maker.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class TableMaker extends Html implements TableMakerInterface
{
    /**
     * Table head.
     * @var THeadInterface
     */
    protected $thead = null;

    /**
     * Table body.
     * @var TBodyInterface
     */
    protected $tbody = null;

    /**
     * Table foot.
     * @var TFootInterface
     */
    protected $tfoot = null;

    /**
     * Format.
     * @var string
     */
    protected $format = null;

    /**
     * Constructor.
     * 
     * @param   array           $params     Parameters.
     * @param   string|null     $format     Format.
     * @return  TableMakerInterface    
     */
    public function __construct($params = [], ?string $format = null)
    {
        $this->format = $format;
        if (array_key_exists('class', $params) and !empty($params['class'])) {
            $params['class'] .= ' tablemaker';
        } else {
            $params['class'] = 'tablemaker';
        }
        if (!is_null($this->format)) {
            $params['class'] .= ' ' . $this->format;
        }
        parent::__construct('table', $params);
    }

    /**
     * Create the head.
     * 
     * @param   array       $params     Parameters.
     * @return  THeadInterface
     */
    public function createHead(array $params = []): THeadInterface
    {
        $this->thead = new THead($this, $params);
        return $this->thead;
    }

    /**
     * Get the table head.
     * 
     * @param   array       $params     Parameters for creation.
     * @return  THeadInterface
     */
    public function thead(array $params = []): THeadInterface
    {
        if (is_null($this->thead)) {
            return $this->createHead($params);
        }
        return $this->thead;
    }

    /**
     * Create the body.
     * 
     * @param   array       $params     Parameters.
     * @return  TBodyInterface
     */
    public function createBody(array $params = []): TBodyInterface
    {
        $this->tbody = new TBody($this, $params);
        return $this->tbody;
    }

    /**
     * Get the table body.
     * 
     * @param   array       $params     Parameters for creation.
     * @return  TBodyInterface
     */
    public function tbody(array $params = []): TBodyInterface
    {
        if (is_null($this->tbody)) {
            return $this->createBody($params);
        }
        return $this->tbody;
    }

    /**
     * Create the foot.
     * 
     * @param   array       $params     Parameters.
     * @return  TFootInterface
     */
    public function createFoot(array $params = []): TFootInterface
    {
        $this->tfoot = new TFoot($this, $params);
        return $this->tfoot;
    }

    /**
     * Get the table foot.
     * 
     * @param   array       $params     Parameters for creation.
     * @return  TFootInterface
     */
    public function tfoot(array $params = []): TFootInterface
    {
        if (is_null($this->tfoot)) {
            return $this->createFoot($params);
        }
        return $this->tfoot;
    }

    /**
	 * Render the table.
	 *
	 * @param 	string|null	$data 	        Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function render(?string $data = null, ?array $extraParams = null) : string
    {
        if ('vertical' == $this->format) {
            $data = $this->tbody->renderVertical();
        } else {
            if (is_null($this->thead)) {
                return null;
            }
            if (is_null($this->tbody)) {
                return null;
            }
            $data = $this->thead->render() . $this->tbody->render();
            if (!is_null($this->tfoot)) {
                $data .= $this->tfoot->render();
            }
        }
        return parent::render($data, $extraParams);
    }
}
