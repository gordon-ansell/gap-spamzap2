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

/**
 * Table maker interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface TableMakerInterface extends HtmlInterface
{
    /**
     * Get the table head.
     * 
     * @param   array       $params     Parameters for creation.
     * @return  THeadInterface
     */
    public function thead(array $params = []): THeadInterface;

    /**
     * Create the body.
     * 
     * @param   array       $params     Parameters.
     * @return  TBodyInterface
     */
    public function createBody(array $params = []): TBodyInterface;

    /**
     * Get the table body.
     * 
     * @param   array       $params     Parameters for creation.
     * @return  TBodyInterface
     */
    public function tbody(array $params = []): TBodyInterface;

    /**
     * Create the foot.
     * 
     * @param   array       $params     Parameters.
     * @return  TFootInterface
     */
    public function createFoot(array $params = []): TFootInterface;

    /**
     * Get the table foot.
     * 
     * @param   array       $params     Parameters for creation.
     * @return  TFootInterface
     */
    public function tfoot(array $params = []): TFootInterface;
}
