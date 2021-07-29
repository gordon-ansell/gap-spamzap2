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
 * Table column.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface ColumnInterface extends HtmlInterface 
{
    /**
     * Get the head.
     * 
     * @return  THeadInterface
     */
    public function getHead(): THeadInterface;

    /**
     * Render the column for the head.
     * 
     * @return  string
     */
    public function renderHead(): string;

    /**
     * Render the column for the body.
     * 
     * @param   string      $data   Data to render.
     * @return  string
     */
    public function renderBody(string $data): string;
}
