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
namespace GreenFedora\Form;

use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Session\SessionInterface;

/**
 * Form persistance handler interface.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

interface FormPersistHandlerInterface
{
    /**
     * Get the session handler.
     * 
     * @return  SessionInterface
     */
    public function getSession(): SessionInterface;

    /**
     * Load the cookies.
     * 
     * @param   ArrInterface    $target     Where to load them.
     * @return  void
     */
    public function load(ArrInterface &$target);

    /**
     * Save the cookies.
     * 
     * @param   ArrInterface    $source     Where to get the data.
     * @return  void
     */
    public function save(ArrInterface $source);
}