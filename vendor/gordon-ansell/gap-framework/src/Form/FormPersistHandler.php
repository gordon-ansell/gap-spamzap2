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

use GreenFedora\Session\SessionInterface;
use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Form\FormPersistHandlerInterface;

/**
 * Form persistance handler.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class FormPersistHandler implements FormPersistHandlerInterface
{

    /**
     * Session.
     * @var SessionInterface
     */
    protected $session = null;

    /**
     * Cookie names and defaults.
     * @var array
     */
    protected $names = [];

    /**
     * Cookie prefix.
     * @var string
     */
    protected $prefix = null;

    /**
     * Constructor.
     * 
     * @param   SessionInterface        $session    Session handler.
     * @param   array                   $names      Cookie names and defaults.
     * @param   string                  $prefix     Cookie prefix.
     * 
     * @return  void
     * 
     * #[Inject (session: session)]
     */
    public function __construct(?SessionInterface $session = null, array $names, string $prefix)
    {
        $this->session = $session;
        $this->names = $names;
        $this->prefix = $prefix;
    }

    /**
     * Get the session handler.
     * 
     * @return  SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * Load the cookies.
     * 
     * @param   ArrInterface    $target     Where to load them.
     * @return  void
     */
    public function load(ArrInterface &$target)
    {
        foreach ($this->names as $key => $default) {
            $val = $this->session->get($this->prefix . $key, $default);
            $target->set($key, $val);
        }
    }

    /**
     * Save the cookies.
     * 
     * @param   ArrInterface    $source     Where to get the data.
     * @return  void
     */
    public function save(ArrInterface $source)
    {
        foreach ($this->names as $key => $default) {
            if ($source->has($key)) {
                $this->session->set($this->prefix . $key, strval($source->get($key)));
            } else {
                $this->session->set($this->prefix . $key, strval($default));
            }
        }
    }
}