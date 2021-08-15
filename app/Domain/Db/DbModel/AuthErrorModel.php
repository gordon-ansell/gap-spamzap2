<?php
/**
 * This file is part of the SpamZap2 package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace App\Domain\Db\DbModel;

use App\Domain\Db\DbModel\AbstractDbModel;

/**
 * Auth error model.
 */
class AuthErrorModel extends AbstractDbModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'autherror';

    /**
     * Add auth error.
     * 
     * @param   string  $ip         IP address.
     * @param   string  $username   Username.
     * 
     * @return  int                 Count of attempts.
     */
    public function addAuthError(string $ip, string $username): int
    {
        $result = $this->getDb()
            ->select($this->tableName)
            ->where('ip', $ip)
            ->where('username', $username)
            ->fetchArray();

        if (count($result) > 0) {

            $res = $result[0];

            $trycount = intval($res['trycount']) + 1;

            $this->update($res[$this->tableName . '_id'], ['trycount' => $trycount]);

            return $trycount;
            
        } else {
            $this->create(['ip' => $ip, 'username' => $username]);

            return 1;
        }

    }

    /**
     * Delete record for IP and user.
     * 
     * @param   string  $ip         IP address.
     * @param   string  $username   Username.
     * 
     * @return  bool
     */
    public function deleteFor(string $ip, string $username)
    {
        $result = $this->getDb()
            ->select($this->tableName)
            ->where('ip', $ip)
            ->where('username', $username)
            ->fetchArray();

        $this->delete($result[0][$this->tableName . '_id']);    
    }

}
