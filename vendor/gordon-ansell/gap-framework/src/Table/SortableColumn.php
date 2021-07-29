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

use GreenFedora\Table\Column;
use GreenFedora\Table\SortableColumnInterface;
use GreenFedora\Html\Html;

/**
 * Sortable table column.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class SortableColumn extends Column implements SortableColumnInterface
{
    /**
     * Render the header.
     * 
     * @return  string
     */
    public function renderHdr(): string
    {
        $p = array(
            'type'      =>  'submit',
            'class'     =>  'sortablecolumn-button',
            'name'      =>  'sortcol',
            'id'        =>  'sortcol-' . $this->name,
            'value'     =>  $this->name
        );
        $b = new Html('button', $p, null, false, false);

        $sort = $this->table->getSort();
        $dirArrow = '';
        $sortDir = 'asc';

        if (null !== $sort and $sort[0] == $this->name) {
            if ('asc' == $sort[1]) {
                $dirArrow = '&#8593;';
                $sortDir = 'desc';
            } else if ('desc' == $sort[1]) {
                $dirArrow = '&#8595;';
                $sortDir = 'off';
            } else {
                $sortDir = 'asc';
            }
        }
        
        $p1 = array(
            'type'      =>  'hidden',
            'name'      =>  'sortdir',
            'id'        =>  'sortdir-' . $this->name,
            'value'     =>  $sortDir
        );
        $b1 = new Html('input', $p1);

        $params = $this->hdrParams;
        if ($this->hdrClass) {
            $params['class'] = $this->hdrClass;
        }

        $h = new Html($this->hdrTag, $params);

        return $h->render($b->render($this->title . $dirArrow) . $b1->render());
    }

}
