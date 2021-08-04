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
use App\Domain\TypeCodes;
use GreenFedora\Uri\Uri;
use GreenFedora\Stdlib\Domain;
use GreenFedora\Stdlib\Path;
use GreenFedora\Html\Html;
use GreenFedora\Table\TableInterface;

/**
 * Auth log model.
 */
class AuthLogModel extends AbstractDbModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'authlog';

    /**
     * Get a simple count of all the records.
     * 
     * @return  int
     */
    public function getSimpleCount(): int
    {
        $tmp = $this->getDb()->select($this->tableName)
            ->count($this->tableName . '_id', 'cnt')
            ->fetchArray();
        return intval($tmp[0]['cnt']);
    }

    /**
     * Count IP instances.
     * 
     * @param   string  $ip     IP to check.
     * @param   string  $prior  Prior to this date,
     * @param   bool    $like   Run as a like?
     * @return  int
     */
    public function countIp(string $ip, string $prior, bool $like = false): int
    {
        $sq = $this->getDb()->select($this->tableName)
            ->count($this->tableName . '_id', 'cnt')
            ->where('dt', $prior, '<');

        if ($like) {
            $sp = explode('.', $ip);
            unset($sp[count($sp) - 1]);
            $r = implode('.', $sp);
            $sq->whereLike('ip', $r . '%');
        } else {
            $sq->where('ip', $ip);
        }
            
        $tmp = $sq->fetchArray();
        return intval($tmp[0]['cnt']);

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
     * Encryption/decryption.
     *
     * @param   string  $stringToHandle  String.
     * @param   string  $secret_key      Key.
     * @param   string  $secret_iv       Vector.
     * @param   bool    $decrypt         Decrypt?
     *
     * @return  string                   Processed string.
     */
    public function cryptic(string $stringToHandle, string $secret_key, string $secret_iv, bool $decrypt = false): string
    {
        // Set default output value.
        $output = null;

        // Hash the secrets.
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        // Check whether encryption or decryption.
        if(!$decrypt) {
           // We are encrypting.
           $output = base64_encode(openssl_encrypt($stringToHandle, "AES-256-CBC", $key, 0, $iv));
        } else {
           // We are decrypting
           $output = openssl_decrypt(base64_decode($stringToHandle), "AES-256-CBC", $key, 0, $iv);
        }

        // Return the final value.
        return $output;
   }

   /**
     * Process the records for display.
     * 
     * @param   array           $records        Log records to process.
     * @param   int             $logNew         Number of new log records.
     * @param   string          $slug           Slug.
     * @return  array
     */
    public function processRecordsForDisplay(array $records, int $logNew, string $slug)
    {

        $data = [];

        $slugUrl = Path::join(\plugin_dir_url($slug), $slug);
        $iconUrl = Path::join($slugUrl, 'assets', 'icons');
        $banUrl = \admin_url('admin.php') . '?page=spamzap2-add-rule&back=authlog';

        // Icons.
        $plusIcon = new Html('img', [
            'title' => 'Expand.', 
            'alt' => "Plus icon.", 
            'class' => 'icon plus',
            'src' => Path::join($iconUrl, 'plus.png'),
        ]);
        $minusIcon = new Html('img', [
            'title' => 'Collapse.', 
            'alt' => "Minus icon.", 
            'class' => 'icon minus',
            'src' => Path::join($iconUrl, 'minus.png'),
        ]);
        $blankIcon = new Html('img', [
            'alt' => "Blank, transparent icon.", 
            'class' => 'icon blank',
            'src' => Path::join($iconUrl, 'blank.png'),
        ]);
        $blankIconR = $blankIcon->render();

        // IP bans.
        $ipBanLink = new Html('a', [
            'href' => $banUrl,
            'title' => 'Ban this IP address.',
        ]);
        $ipBanIcon = new Html('img', [
            'title' => 'Ban this IP address.', 
            'alt' => "Generic block icon, indicating IP address to be banned.", 
            'class' => 'icon banip',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $ipBanIconR = $ipBanIcon->render();

        // IP CIDR bans.
        $cidrBanIcon = new Html('img', [
            'title' => 'Ban this IP CIDR range.', 
            'alt' => "Generic block icon, indicating IP CIDR range to be banned.", 
            'class' => 'icon bancidr',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $cidrBanIconR = $cidrBanIcon->render();

        $count = 1;
        foreach ($records as $record) {

            // New records.
            if ($count <= $logNew) {
                $record['dt'] = '<span class="newrec">' . $record['dt'] . '</span>'; 
            }

            // +/- icons.
            $record['dt'] = $plusIcon->render(null, [
                    'class' => 'plus-' . $count, 
                    'id' => 'eplus-' . $count,
                    'onclick' => "expandlogrecord(" . $count . ")"
                ]) 
                . $minusIcon->render(null, [
                    'class' => 'minus-' . $count, 
                    'id' => 'eminus-' . $count,
                    'onclick' => "expandlogrecord(" . $count . ")"
                    ]) 
                . $record['dt'];

            // IP address seen?
            if (0 != $record['seenip'] or 0 != $record['seen24']) {
                if (0 != $record['seenip']) {
                    $record['ip'] = '<span class="ipseen">' . $record['ip'] . '</span>';
                } else {
                    $sp = explode('.', $record['ip']);
                    $record['ip'] = '<span class="ipseen">' . $sp[0] . '.' . $sp[1] . '.' . $sp[2] . '</span>.' . $sp[3]; 
                }
            }

            // IP icons.
            if ('' != $record['ip2'] and 'n/a' != $record['ip2']) {
                $ipBanLink->setParam('href', $banUrl . '&ip=' . $record['ip2']);
                $record['ip2'] = $ipBanLink->render($ipBanIconR) . $record['ip']; 
            }

            // CIDR icons.
            if ('' != $record['cidrs'] and 'n/a' != $record['cidrs']) {
                $ipBanLink->setParam('href', $banUrl . '&ip=' . $record['cidrs']);
                $record['cidrs'] = $ipBanLink->render($cidrBanIconR) . $record['cidrs']; 
            }

            // Password.
            $info = $record['pwd'];
            if (!is_null($info) and '' != $info) {
                $settings = $this->dbAccess->getSettings();
                $secret_key = $settings['secret-key'];
                $secret_iv = $settings['secret-iv'];
                if ("1" == $settings['decrypt'] and !empty($secret_key) and !empty($secret_iv)) {
                    $info = $info . ' = ' . $this->cryptic($info, $secret_key, $secret_iv, true);
                }
                $record['pwd'] = $info;
            } else {
                $record['pwd'] = '';
            }

            $data[] = $record;
            $count++;
        }

        return $data;

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
        if (is_null($order)) {
            $order = $this->tableName . '_id';
        }
        $settings = $this->dbAccess->getSettings();

        $data = $this->getDb()->select($this->tableName)
            ->order('dt', 'desc')
            ->join('iplookup', 'iplookup_id', 'authlog', 'ip')
            ->limit(intval($settings['log-lines']), $start)
            ->fetchArray();

        $result = [];

        foreach ($data as $record) {

            $ip = $record['ip'];
            if ('::1' == $ip) {
                $ip = '127.0.0.1';
            }
            if (is_null($ip)) {
                $ip = 'n/a';
            }   

            // See how many times we've seen this IP and class 24.
            $seenip = 0;
            $seen24 = 0;
            if ('n/a' != $ip and '127.0.0.1' != $ip) {
                $seenip = $this->countIp($ip, $record['dt']);
                $seen24 = $this->countIp($ip, $record['dt'], true);
            }
            
            $sip = $seenip . '/' . $seen24;

            $new = [
                'dt'            =>  $this->convDt($record['dt']),
                'username'      =>  is_null($record['username']) ? '' : $record['username'],   
                'userid'        =>  is_null($record['userid']) ? 0 : $record['userid'],   
                'userexists'    =>  (0 == $record['userid']) ? 'No' : 'Yes',   
                'ip'            =>  $ip,

                'username2'         =>  is_null($record['username']) ? '' : $record['username'],      
                'pwd'               =>  is_null($record['pwd']) ? '' : $record['pwd'],

                'ip2'               =>  $ip,
                'seen'              =>  $sip,
                'cidrs'             =>  $record['ipl_cidrs'] ?? '',
                'name'              =>  $record['ipl_name'] ?? '',
                'netname'           =>  $record['ipl_netname'] ?? '',
                'address'           =>  $record['ipl_address'] ?? '',
                'country'           =>  $record['ipl_country'] ?? '',
                'domain'            =>  $record['ipl_domain'] ?? '',
                'networkstatus'     =>  $record['ipl_networkstatus'] ?? '',
                'seenip'            =>  $seenip,
                'seen24'            =>  $seen24,
            ];

            $result[] = $new;
        }

        return $result;
    }
}
