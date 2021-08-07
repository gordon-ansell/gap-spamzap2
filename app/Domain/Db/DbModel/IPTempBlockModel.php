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

use App\Domain\Db\DbModel\IPBlockModel;
use App\Domain\TypeCodes;

/**
 * IP temp block model.
 */
class IPTempBlockModel extends IPBlockModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'iptempblock';

    /**
     * Expire necessary entries.
     * 
     * @return int  Number of entries expired.
     */
    public function expireEntries(): int
    {
        $settings = $this->dbAccess->getSettings();
        $expireDays = intval($settings['temp-block-days']);

        $now = new \DateTime("now", new \DateTimeZone('UTC'));

        $recs = $this->listAll();

        $count = 0;
        foreach ($recs as $rec) {
            $dt = new \DateTime($rec['dt'], new \DateTimeZone('UTC'));
            $dt->add(new \DateInterval('P' . $expireDays . 'D'));

            if ($now >= $dt) {

                $lm = app()->get('logmodel');
                $logrec = [
                    'type' => TypeCodes::TYPE_INFO,
                    'matchtype' => TypeCodes::MT_EXPIRE_RULE, 
                    'matchval' => 'IP Temp Block: ' . $rec['ip'],
                    'dt' => $this->getDt(),
                    'status' => TypeCodes::STATUS_INFO,
                ];
                $lm->create($logrec);    

                $this->delete(intval($rec['iptempblock_id']));
                $count++;

            }
        }

        return $count;

    }

}
