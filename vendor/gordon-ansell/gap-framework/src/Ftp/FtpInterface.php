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

/**
 * Interface for the Ftp class.
 */
interface FtpInterface
{
    /**
     * Connect.
     * 
     * @return  self
     * @throws  InvalidArgumentException
     * @throws  RuntimeException
     */
    public function connect(): FtpInterface;

    /**
     * Login.
     * 
     * @return  self
     * @throws  RuntimeException
     */
    public function login(): FtpInterface;

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
    public function put(string $source, string $target, int $mode = FTP_ASCII, int $offset = 0): FtpInterface;

    /**
     * Close connection.
     * 
     * @return  void
     */
    public function close();
}
