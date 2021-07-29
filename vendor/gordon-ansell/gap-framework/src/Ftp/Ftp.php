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
namespace GreenFedora\Ftp;

use GreenFedora\Ftp\Exception\InvalidArgumentException;
use GreenFedora\Ftp\Exception\RuntimeException;
use GreenFedora\Ftp\FtpInterface;

/**
 * Description for the Ftp class.
 */
class Ftp implements FtpInterface
{
    /**
     * Spec.
     * @var array
     */
    protected $spec = [];

    /**
     * User.
     * @var string
     */
    protected $user = null;

    /**
     * Password.
     * @var string
     */
    protected $pass = null;

    /**
     * Connection resource.
     * @var resource
     */
    protected $conn = null;

    /**
     * Constructor.
     * 
     * @param   array   $spec   FTP spec.
     * @param   string  $user   User.
     * @param   string  $pass   Password.
     * @return  void
     */
    public function __construct(array $spec, string $user, string $pass)
    {
        $this->spec = $spec;
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Connect.
     * 
     * @return  self
     * @throws  InvalidArgumentException
     * @throws  RuntimeException
     */
    public function connect(): FtpInterface
    {
        if (!array_key_exists('hostname', $this->spec)) {
            throw new InvalidArgumentException("No hostname specified.");
        }
        if (!array_key_exists('port', $this->spec)) {
            throw new InvalidArgumentException("No port specified.");
        }
        if (!array_key_exists('timeout', $this->spec)) {
            throw new InvalidArgumentException("No timeout specified.");
        }

        $this->conn = ftp_connect($this->spec['hostname'], $this->spec['port'], $this->spec['timeout']);

        if (false === $this->conn) {
            throw new RuntimeException(sprintf('Failed to connect to %s via FTP.', $this->spec['hostname']));
        }

        return $this;
    }

    /**
     * Login.
     * 
     * @return  self
     * @throws  RuntimeException
     */
    public function login(): FtpInterface
    {
        if (!@ftp_login($this->conn, $this->user, $this->pass)) {
            throw new RuntimeException(sprintf("Failed to log into %s@%s with the provided poassword.", 
                $this->user, $this->spec['hostname']));
        }
        return $this;
    }

    /**
     * Transmit a file to the remote.
     * 
     * @param   string      $source     Source file.
     * @param   string      $target     Remote file.
     * @param   int         $mode       File mode.
     * @param   int         $offset     File offset.
     * @return  self
     * @throws  RuntimeException
     */
    public function put(string $source, string $target, int $mode = FTP_ASCII, int $offset = 0): FtpInterface
    {
        $currDir = ftp_pwd($this->conn);

        $target = trim($target, DIRECTORY_SEPARATOR);

        if (false !== strpos($target, DIRECTORY_SEPARATOR)) {
            $sp = explode(DIRECTORY_SEPARATOR, $target);
            $fn = array_pop($sp);
            for ($i = 0; $i < count($sp); $i++) {
                if (!@ftp_chdir($this->conn, $sp[$i])) {
                    ftp_mkdir($this->conn, $sp[$i]);
                    ftp_chdir($this->conn, $sp[$i]);
                }
            }
            
            if (!@ftp_put($this->conn, $fn, $source, $mode, $offset)) {
                throw new RuntimeException(sprintf("Unable to FTP '%s', to '%s'.", $source, $target));
            }
        } else {
            if (!@ftp_put($this->conn, $target, $source, $mode, $offset)) {
                throw new RuntimeException(sprintf("Unable to FTP '%s', to '%s'.", $source, $target));
            }
        }

        ftp_chdir($this->conn, $currDir);

        return $this;
    }

    /**
     * Close connection.
     * 
     * @return  void
     */
    public function close()
    {
        ftp_close($this->conn);
    }

    /**
     * Destructor.
     * 
     * @return  void
     */
    public function __destruct()
    {
        $this->close();
    }
}
