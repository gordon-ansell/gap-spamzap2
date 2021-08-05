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
use GreenFedora\IP\IPAddress;

/**
 * Tech log model.
 */
class TechLogModel extends AbstractDbModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'techlog';

    /**
     * Add an error.
     * 
     * @param   string  $msg    Message.
     * @param   mixed   $extra  Extra.
     * 
     * @return  void
     */
    public function addError(string $msg, $extra = '')
    {
        $ip = IPAddress::getClientIp();
        $data = [
            'dt'        => $this->getDt(),
            'ip'        => $ip,
            'type'      => 1,
            'message'   => $msg,
            'extra'     => '',
        ];

        $this->create($data);
    }

    /**
     * Add a debug message.
     * 
     * @param   string  $msg    Message.
     * @param   mixed   $extra  Extra.
     * 
     * @return  void
     */
    public function addDebug(string $msg, $extra = '')
    {
        $ip = IPAddress::getClientIp();
        $data = [
            'dt'        => $this->getDt(),
            'ip'        => $ip,
            'type'      => 2,
            'message'   => $msg,
            'extra'     => '',
        ];

        $this->create($data);
    }

    /**
     * Add an info message.
     * 
     * @param   string  $msg    Message.
     * @param   mixed   $extra  Extra.
     * 
     * @return  void
     */
    public function addInfo(string $msg, $extra = '')
    {
        $ip = IPAddress::getClientIp();
        $data = [
            'dt'        => $this->getDt(),
            'ip'        => $ip,
            'type'      => 3,
            'message'   => $msg,
            'extra'     => '',
        ];

        $this->create($data);
    }

    /**
     * Get the record count.
     * 
     * @return  int
     */
    public function recordCount(): int
    {
        $sq = $this->getDb()->select($this->tableName)
            ->count($this->tableName . '_id', 'cnt');

        $tmp = $sq->fetchArray();
        return intval($tmp[0]['cnt']);
    }

    /**
     * List all entries.
     * 
     * @param   int      $start      Start record.
     * @param   string   $order      Column to order by.
     * @return  array
     */
    public function getRecords(int $start = 0, ?string $order = null): array
    {
        $settings = $this->dbAccess->getSettings();

        $data = $this->getDb()->select($this->tableName)
            ->order('dt', 'desc')
            ->limit(intval($settings['log-lines']), $start)
            ->fetchArray();

        return $data;
    }
}
