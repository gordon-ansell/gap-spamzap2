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
use GreenFedora\Html\TableMaker\TBodyInterface;
use GreenFedora\Html\TableMaker\RowInterface;

/**
 * Table subrow interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface SubRowInterface extends HtmlInterface
{
    /**
     * Get the parent row.
     * 
     * @return TBodyInterface
     */
    public function getRow(): RowInterface;

    /**
     * Get the data.
     * 
     * @return  TableMakerInterface
     */
    public function getData(): TableMakerInterface;
}
