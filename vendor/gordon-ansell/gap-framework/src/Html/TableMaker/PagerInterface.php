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

use GreenFedora\Html\HtmlInterface;
use GreenFedora\Html\TableMaker\THeadInterface;

/**
 * Table pager interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface PagerInterface extends HtmlInterface 
{
    /**
     * Get the start record.
     * 
     * @return  int
     */
    public function startRec(): int;
}
