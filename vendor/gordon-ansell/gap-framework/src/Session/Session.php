<?php
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Session;

use GreenFedora\Session\SessionInterface;
use GreenFedora\Stdlib\Arr\Arr;
use GreenFedora\Stdlib\Arr\ArrInterface;
use GreenFedora\Stdlib\Path;

/**
 * Session handler.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Session implements SessionInterface
{
	/**
	 * Configs.
	 * @var ArrInterface
	 */
	protected $cfg = null;	 

	/**
	 * Constructor.
	 *
     * @param   iterable     		$cfg            Template specifications.
     * @param   bool                $init           Initiate?
	 *
	 * @return 	void
     * #[Inject (cfg: cfg|logger)]
	 */
	public function __construct(?iterable $cfg = null, bool $init = true)
	{
        throw new \Exception("DO NOT CREATE SESSIONS");
		$this->cfg = new Arr($cfg);

        if ($init) {
            $this->init();
        }
	}	

    /**
     * Initiate.
     * 
     * @return void
     */
    protected function init()
    {
        if (headers_sent()) {
            throw new \Exception("Session headers already sent.");
	        return;
        }

        $lifetime = $this->cfg->get('cookie_lifetime', '0');

        if (session_status() != PHP_SESSION_ACTIVE) {
            
            $gcml = $this->cfg->get('gc_maxlifetime', '7200');
    
            ini_set('session.cookie_lifetime', $lifetime);
            ini_set('session.gc_maxlifetime', $gcml);
            ini_set('session.gc_probability', $this->cfg->get('gc_probability', '1'));
            ini_set('session.gc_divisor', $this->cfg->get('gc_divisor', '100'));
            
            $sp = $this->cfg->get('save_path', '');
            if ('' === $sp) {
                $sp = Path::join(app()->getConfig('env.BASEPATH'), "sessions");
            }

            if (!file_exists($sp)) {
                mkdir($sp, 0777, true);
                ini_set('session.save_path', $sp);
            }
        }

        $path = $this->cfg->get('cookie_path', '/');

        register_shutdown_function('session_write_close');

        if (session_status() == PHP_SESSION_NONE) {
            session_set_cookie_params($lifetime, $path);
            if (!session_start(array('cookie_lifetime' => $lifetime))) {
                $this->clear();
                if (!session_start(array('cookie_lifetime' => $lifetime))) {
                    return;
                }
            }
        }
        
		setcookie(session_name(), session_id(), time() + $lifetime, $path);
		$this->set('time', time());

    }

    /**
     * Get the session prefix.
     * 
     * @return  string
     */
    public function getPrefix() : string
    {
        return $this->prefix;
    }

    /**
     * Regenerate a session ID.
     *
     * @return  void
     */
    public function regenerateId()
    {
        session_regenerate_id(true);
    }

    /**
     * Clear all session data.
     *
     * @return  SessionInterface
     */
    public function clear() : SessionInterface
    {
        $this->regenerateId();
        session_unset();
        session_destroy();
        return $this;
    }

    /**
     * Unset a session variable.
     *
     * @param   string          $key        Key to unset.
     * @return  SessionInterface
     */
    public function unset(string $key) : SessionInterface
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return $this;
    }

    /**
     * Generate a csrf token.
     * 
     * @return  string
     */
    public function csrfToken1() : string
    {
        if (empty($_SESSION['csrf_token1'])) {
            $_SESSION['csrf_token1'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token1'];
    }

    /**
     * Generate a csrf token.
     * 
     * @param   string      $name       Name to use.
     * @return  string
     */
    public function csrfToken2(string $name) : string
    {
        if (empty($_SESSION['csrf_token2'])) {
            $_SESSION['csrf_token2'] = random_bytes(32);
        }
        return hash_hmac('sha256', $name, $_SESSION['csrf_token2']);
    }

    /**
     * Add a flash key.
     * 
     * @param   string          $key        Key to add.
     * @return  void
     */
    protected function addFlashKey(string $key)
    {
        if (!$this->has('flashKeys')) {
            $_SESSION['flashKeys'] = array($key);
        } else {
            $fk = $_SESSION['flashKeys'];
            if (!in_array($key, $fk)) {
                $fk[] = $key;
                $_SESSION['flashKeys'] = $fk;
            }
        }
    }

    /**
     * Remove a flash key.
     * 
     * @param   string          $key        Key to remove.
     * @return  void
     */
    protected function removeFlashKey(string $key)
    {
        if ($this->has('flashKeys')) {
            $fk = $_SESSION['flashKeys'];
            if (in_array($key, $fk)) {
                $fk = array_diff($fk, array($key));
                $_SESSION['flashKeys'] = $fk;
                $this->unset($key);
            }
        }
    }

    /**
     * Set a session variable.
     *
     * @param   string          $key        Key to set.
     * @param   mixed           $val        Value to set.
     * @param   bool            $flash      Flash data?
     * @return  SessionInterface
     */
    public function set(string $key, $val, bool $flash = false) : SessionInterface
    {
        $_SESSION[$key] = $val;
        if ($flash) {
            $this->addFlashKey($key);
        }
        return $this;
    }

    /**
     * Set a flash session variable.
     *
     * @param   string          $key        Key to set.
     * @param   mixed           $val        Value to set.
     * @return  SessionInterface
     */
    public function setFlash(string $key, $val) : SessionInterface
    {
        return $this->set($key, $val, true);
    }

    /**
     * Get a session variable.
     *
     * @param   string          $key        Key to set.
     * @param   mixed           $def        Default to get if not set.
     * @param   bool            $clearFlash Clear down flash data if this is a flash entry.
     * @return  mixed
     */
    public function get(string $key, $def = null, bool $clearFlash = true)
    {
        if (isset($_SESSION[$key])) {
            $ret = $_SESSION[$key];
        } else {
            $ret = $def;
        }

        if ($clearFlash) {
            $this->removeFlashKey($key);
        }

        return $ret;
    }

    /**
     * Get all the session keys unprefixed.
     * 
     * @param   string          $pref       Prefix.
     * @return  array
     */
    public function getAllUnprefixed(string $pref = ''): array
    {
        $ret = [];
        foreach ($_SESSION as $key => $val) {
            if ('' != $pref) {
                if (substr($key, 0, strlen($pref)) == $pref) {
                    $k = substr($key, strlen($pref));
                    $ret[$k] = $this->get($key);
                }
            } else {
                $ret[$key] = $this->get($key);
            }
        }
        return $ret;
    }

    /**
     * See if we have a session variable.
     *
     * @param   string          $key        Key to check.
     * @return  bool
     */
    public function has(string $key) : bool
    {
        if (isset($_SESSION[$key])) {
            return true;
        }

        return false;
    }
}
