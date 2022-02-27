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
 * Log model.
 */
class LogModel extends AbstractDbModel
{
    /**
     * Table name.
     * @var string
     */
    protected $tableName = 'log';

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
     * Get the counts.
     * 
     * @return
     */
    public function getCounts()
    {
        $ret = [
            'comment'   =>  0,
            'reg'       =>  0,
            'contact'   =>  0,
        ];

        $tmp = $this->getDb()->select($this->tableName)
            ->count($this->tableName . '_id', 'cnt')
            ->where('type', TypeCodes::TYPE_COMMENT)
            ->where('status', TypeCodes::STATUS_ALLOW)
            ->fetchArray();
        $ret['comment'] = intval($tmp[0]['cnt']);

        $tmp = $this->getDb()->select($this->tableName)
            ->count($this->tableName . '_id', 'cnt')
            ->where('type', TypeCodes::TYPE_REG)
            ->where('status', TypeCodes::STATUS_ALLOW)
            ->fetchArray();
        $ret['comment'] = intval($tmp[0]['cnt']);


        return $ret;
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
    * Create all the icons.
    * 
    * @param    string  $slug   Slug.
    * 
    * @return   array
    */
    protected function createIcons(string $slug): array
    {
        $slugUrl = Path::join(\plugin_dir_url($slug), $slug);
        $iconUrl = Path::join($slugUrl, 'assets', 'icons');

        // +/- icons.
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


        // Type icons.
        $infoIcon = new Html('img', [
            'title' => 'Info record. Adding rules etc.', 
            'alt' => "Info icon.", 
            'class' => 'icon info',
            'src' => Path::join($iconUrl, 'info.png'),
        ]);
        $infoIconR = $infoIcon->render();
        $contactIcon = new Html('img', [
            'title' => 'Contact record.', 
            'alt' => "Contact icon.", 
            'class' => 'icon contact',
            'src' => Path::join($iconUrl, 'contact.png'),
        ]);
        $contactIconR = $contactIcon->render();
        $commentIcon = new Html('img', [
            'title' => 'Comment record', 
            'alt' => "Comment icon.", 
            'class' => 'icon comment',
            'src' => Path::join($iconUrl, 'comment.png'),
        ]);
        $commentIconR = $commentIcon->render();
        $regIcon = new Html('img', [
            'title' => 'Registration record', 
            'alt' => "Registration icon.", 
            'class' => 'icon registration',
            'src' => Path::join($iconUrl, 'registration.png'),
        ]);
        $regIconR = $regIcon->render();
        $passIcon = new Html('img', [
            'title' => 'Password recovery record', 
            'alt' => "Password recovery icon.", 
            'class' => 'icon password',
            'src' => Path::join($iconUrl, 'password.png'),
        ]);
        $passIconR = $passIcon->render();
        $lgnIcon = new Html('img', [
            'title' => 'Login record', 
            'alt' => "Login icon.", 
            'class' => 'icon login',
            'src' => Path::join($iconUrl, 'login.png'),
        ]);
        $lgnIconR = $lgnIcon->render();

        // Delete user.
        $delUserIcon = new Html('img', [
            'title' => 'Delete user', 
            'alt' => "Delete user icon.", 
            'class' => 'icon deluser',
            'src' => Path::join($iconUrl, 'del-user.png'),
        ]);
        $delUserIconR = $delUserIcon->render();

        // IP ban.
        $ipBanIcon = new Html('img', [
            'title' => 'Ban this IP address.', 
            'alt' => "Generic block icon, indicating IP address to be banned.", 
            'class' => 'icon banip',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $ipBanIconR = $ipBanIcon->render();

        // Temp ban icon.
        $ipTempBanIcon = new Html('img', [
            'title' => 'Temporarily ban this IP address.', 
            'alt' => "Generic block icon, indicating IP address to be banned.", 
            'class' => 'icon banip',
            'src' => Path::join($iconUrl, 'temp-block.png'),
        ]);
        $ipTempBanIconR = $ipTempBanIcon->render();

        // IP CIDR bans.
        $cidrBanIcon = new Html('img', [
            'title' => 'Ban this IP CIDR range.', 
            'alt' => "Generic block icon, indicating IP CIDR range to be banned.", 
            'class' => 'icon bancidr',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $cidrBanIconR = $cidrBanIcon->render();

        // IP CIDR temp bans.
        $cidrTempBanIcon = new Html('img', [
            'title' => 'Temporarily ban this IP CIDR range.', 
            'alt' => "Generic block icon, indicating IP CIDR range to be banned.", 
            'class' => 'icon bancidr',
            'src' => Path::join($iconUrl, 'temp-block.png'),
        ]);
        $cidrTempBanIconR = $cidrTempBanIcon->render();

        // Domain.
        $domainBanIcon = new Html('img', [
            'title' => 'Ban this domain.', 
            'alt' => "Generic block icon, indicating domain to be banned.", 
            'class' => 'icon bandomain',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $domainBanIconR = $domainBanIcon->render();

        // Email.
        $emailBanIcon = new Html('img', [
            'title' => 'Ban this email address.', 
            'alt' => "Generic block icon, indicating email address to be banned.", 
            'class' => 'icon banemail',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $emailBanIconR = $emailBanIcon->render();


        return array(
            $plusIcon, 
            $minusIcon,
            $blankIconR,
            $infoIconR,
            $contactIconR,
            $commentIconR,
            $regIconR,
            $passIconR,
            $lgnIconR,
            $delUserIconR,
            $ipBanIconR,
            $ipTempBanIconR,
            $cidrBanIconR,
            $cidrTempBanIconR,
            $domainBanIconR,
            $emailBanIconR,
        );

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

        //$slugUrl = Path::join(\plugin_dir_url($slug), $slug);
        //$iconUrl = Path::join($slugUrl, 'assets', 'icons');
        $banUrl = \admin_url('admin.php') . '?page=spamzap2-add-rule';
        $tempBanUrl = \admin_url('admin.php') . '?page=spamzap2-add-rule&temp=true';
        $delUserUrl = \admin_url('users.php') . '?s=';

        $settings = $this->dbAccess->getSettings();

        list(
            $plusIcon, 
            $minusIcon,
            $blankIconR,
            $infoIconR,
            $contactIconR,
            $commentIconR,
            $regIconR,
            $passIconR,
            $lgnIconR,
            $delUserIconR,
            $ipBanIconR,
            $ipTempBanIconR,
            $cidrBanIconR,
            $cidrTempBanIconR,
            $domainBanIconR,
            $emailBanIconR,
        ) = $this->createIcons($slug);

        $delUserLink = new Html('a', [
            'href' => $delUserUrl,
            'target' => '_blank',
            'title' => 'Delete this user.',
        ]);

        // Matchtype class.
        //$matchTypeSpan = new Html('span', ['class' => 'matchtype']);

        // IP bans.
        $ipBanLink = new Html('a', [
            'href' => $banUrl,
            'title' => 'Ban this IP address.',
        ]);

        // IP temp bans.
        $ipTempBanLink = new Html('a', [
            'href' => $tempBanUrl,
            'title' => 'Temporarily ban this IP address.',
        ]);

        // Domain ban.
        $domainBanLink = new Html('a', [
            'href' => $banUrl,
            'title' => 'Ban this domain.',
        ]);

        // Email ban.
        $emailBanLink = new Html('a', [
            'href' => $banUrl,
            'title' => 'Ban this email address.',
        ]);

        $count = 1;
        foreach ($records as $record) {

            // New records.
            if ($count <= $logNew) {
                $record['dt'] = '<span class="newrec">' . $record['dt'] . '</span>'; 
            }

            // +/- icons.
            if ('Inf' != $record['type']) {
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
            } else {
                $record['dt'] = $blankIconR . $record['dt'];
            }

            // Dup?
            if (intval($record['dupcount']) > 1) {
                $record['dt'] = $record['dt'] . ' (' . $record['dupcount'] . ')';
            }

            // Type.
            $record['rawtype'] = $record['type'];
            if ('Inf' == $record['type']) {
                $record['type'] = $infoIconR;
            } else if ('Com' == $record['type']){
                $record['type'] = $commentIconR;
            } else if ('Con' == $record['type']){
                $record['type'] = $contactIconR;
            } else if ('Reg' == $record['type']){
                $record['type'] = $regIconR;
            } else if ('Pass' == $record['type']){
                $record['type'] = $passIconR;
            } else if ('Lgn' == $record['type']){
                $record['type'] = $lgnIconR;
            }

            // IP address seen?
            if (0 != $record['seenip'] or 0 != $record['seen24']) {
                if (0 != $record['seenip']) {
                    $record['ip'] = '<span class="ipseen">' . $record['ip'] . '</span>';
                } else {
                    $sp = explode('.', $record['ip']);
                    $record['ip'] = '<span class="ipseen">' . $sp[0] . '.' . $sp[1] . '.' . $sp[2] . '</span>.' . $sp[3]; 
                }
            }

            // Match type for URL.
            $mtencoded = urlencode($record['matchtype']);

            // IP icons.
            if ('' != $record['ip2'] and 'n/a' != $record['ip2']) {
                $ipBanLink->setParam('href', $banUrl . '&ip=' . $record['ip2'] . '&desc=' . $mtencoded);
                $ipTempBanLink->setParam('href', $tempBanUrl . '&ip=' . $record['ip2'] . '&desc=' . $mtencoded);
                $record['ip2'] = $ipBanLink->render($ipBanIconR) . ' ' . $ipTempBanLink->render($ipTempBanIconR) . $record['ip']; 
            }

            // CIDR icons.
            if ('' != $record['cidrs'] and 'n/a' != $record['cidrs']) {
                $ipBanLink->setParam('href', $banUrl . '&ip=' . $record['cidrs'] . '&desc=' . $mtencoded);
                $ipTempBanLink->setParam('href', $tempBanUrl . '&ip=' . $record['cidrs'] . '&desc=' . $mtencoded);
                $record['cidrs'] = $ipBanLink->render($cidrBanIconR) . ' ' . $ipTempBanLink->render($cidrTempBanIconR) . $record['cidrs']; 
            }

            // Domain block (email).
            if ('' != $record['emaildomain'] and 'n/a' != $record['emaildomain']) {
                $domainBanLink->setParam('href', $banUrl . '&domain=' . $record['emaildomain'] . '&desc=' . $mtencoded);
                $record['emaildomain'] = $domainBanLink->render($domainBanIconR) . $record['emaildomain']; 
            }
            if ('' != $record['rawemaildomain'] and 'n/a' != $record['rawemaildomain']) {
                $domainBanLink->setParam('href', $banUrl . '&domain=' . $record['rawemaildomain'] . '&desc=' . $mtencoded);
                $record['rawemaildomain'] = $domainBanLink->render($domainBanIconR) . $record['rawemaildomain']; 
            }

            // Domain block (authorurl).
            if ('' != $record['commentauthordom'] and 'n/a' != $record['commentauthordom']) {
                $domainBanLink->setParam('href', $banUrl . '&domain=' . $record['commentauthordom'] . '&desc=' . $mtencoded);
                $record['commentauthordom'] = $domainBanLink->render($domainBanIconR) . $record['commentauthordom']; 
            }

            // Domain block (comment)
            if ('' != $record['commentdomains']) {
                $arr = explode(',', $record['commentdomains']);
                $cd = '';
                foreach ($arr as $item) {
                    if ('' != $cd) {
                        $cd .= ', ';
                    }
                    $domainBanLink->setParam('href', $banUrl . '&domain=' . $item . '&desc=' . $mtencoded);
                    $cd .= $domainBanLink->render($domainBanIconR) . $item;
                }
                $record['commentdomains'] = $cd;
            }

            // Email block.
            if ('' != $record['email2'] and 'n/a' != $record['email2']) {
                $emailBanLink->setParam('href', $banUrl . '&email=' . $record['email2'] . '&desc=' . $mtencoded);
                $record['email2'] = $emailBanLink->render($emailBanIconR) . $record['email2']; 
            }

            // User.
            if ('0' != strval($record['userid'])) {
                $tmp = $delUserLink;
                $tmp->setParam('href', $tmp->getParam('href') . strval($record['username2']));
                $record['username2'] = $record['username2'] . $tmp->render($delUserIconR);
            } else {
                $userinfo = \get_user_by('login', strval($record['username2']));
                if (false !== $userinfo) {
                    $tmp = $delUserLink;
                    $tmp->setParam('href', $tmp->getParam('href') . strval($userinfo->ID));
                    $record['username2'] = $record['username2'] . $tmp->render($delUserIconR);
                }
            }

            // Passwords.
            if (!empty($record['pwd']) and '1' == $settings['collect-password'] and !empty($settings['secret1']) and !empty($settings['secret2'])) {
                $record['pwd'] = $this->cryptic($record['pwd'], $settings['secret1'], $settings['secret2'], true);
            } else {
                $record['pwd'] = 'n/a';
            }

            // Info (passwords maybe).
            /*
            $info = $record['info'];
            if (!is_null($info) and '' != $info) {
                $settings = $this->dbAccess->getSettings();
                $secret_key = $settings['secret-key'];
                $secret_iv = $settings['secret-iv'];
                if ("1" == $settings['decrypt'] and !empty($secret_key) and !empty($secret_iv)) {
                    $info = $info . ' = ' . $this->cryptic($info, $secret_key, $secret_iv, true);
                }
                $record['info'] = $info;
            } else {
                $record['info'] = '';
            }
            */

            // Matchtype class.
            /*
            $record['matchtype'] = $matchTypeSpan->render($record['matchtype'], [
                'class' => 'status-' . strtolower($record['status'])
            ]);
            */

            $data[] = $record;
            $count++;
        }

        return $data;

    }

    /**
     * Create an entry.
     * 
     * @param   array         $data       Data to create record with.
     * @return  mixed                     Return the insert ID.
     */
    public function create(array $data)
    {
        $settings = $this->dbAccess->getSettings();

        if ('1' == $settings['roll-up-duplicates']) {

            $latest = $this->getLatestRecord();

            if (!is_null($latest) and TypeCodes::TYPE_COMMENT != $data['type']) {
                $unc = $data['username'] ?? '';
                $unl = $latest['username'] ?? '';
                $ipc = $data['ip'] ?? '';
                $ipl = $latest['ip'] ?? '';
                $passc = $data['pass'] ?? '';
                $passl = $latest['pass'] ?? '';
                $emc = $data['email'] ?? '';
                $eml = $latest['email'] ?? '';
                if (
                    $ipc == $ipl and
                    $unc == $unl and
                    $passc == $passl and
                    $emc == $eml and
                    intval($data['type']) == intval($latest['type']) and
                    intval($data['status']) == intval($latest['status']) and
                    intval($data['matchtype']) == intval($latest['matchtype']) and
                    $data['matchval'] == $latest['matchval']
                ) {
                    $this->incrementCount(intval($latest[$this->tableName . '_id']), $data['dt']);
                } else {
                    return parent::create($data);
                }

            } else {
                return parent::create($data);
            }

        } else {
            return parent::create($data);
        }
    }

    /**
     * Increment the count for a log record.
     * 
     * @param   int     $id     ID.
     * @param   string  $dt     Date/time.
     * @return  void
     */
    public function incrementCount(int $id, string $dt)
    {
        $curr = $this->getDb()->select($this->tableName)
            ->where($this->tableName . '_id', $id)
            ->fetchColumnOfFirstRecord('dupcount');

        $new = $curr + 1;

        $this->update($id, ['dupcount' => $new, 'dt' => $dt]);
    }

    /**
     * Get the latest record.
     * 
     * @return array|null
     */
    public function getLatestRecord(): ?array
    {
        $data = $this->getDb()->select($this->tableName)
            ->order('dt', 'desc')
            ->limit(1)
            ->fetchFirst();

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
            ->join('iplookup', 'iplookup_id', 'log', 'ip')
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
            
            // Extract domains from authorurl.
            $authorurldomain = '';
            $au = $record['commentauthorurl'];
            if (!is_null($au) and !empty($au) and 'n/a' != $au) {
                $uri = new Uri($au);
                $authorurldomain = Domain::extractRawDomain($uri->getDomain());
            }

            // Extract domains from email.
            $emaildomain = '';
            $rawemaildomain = '';
            $ed = $record['email'];
            if (!is_null($ed) and !empty($ed) and 'n/a' != $ed) {
                $sp = explode('@', $ed);
                $emaildomain = str_replace('www.', '', $sp[1]);
                $rawemaildomain = Domain::extractRawDomain($ed);
            }

            $comment = is_null($record['comment']) ? '' : stripslashes($record['comment']);

            // Extract domains from comment.
            $commentDomains = '';
            if (is_null($record['commentdomains'])) {
                if ('' != $comment) {
                    $cd = Domain::domainsFromString($comment);
                    $commentDomains = implode(',', array_unique($cd));
                }
            } else {
                $commentDomains = $record['commentdomains'];
            }

            $comment = substr(htmlentities($comment), 0, intval($settings['comment-chars']));

            $sip = $seenip . '/' . $seen24;

            $usernameTemp = is_null($record['username']) ? '' : $record['username'];
            if (strlen($usernameTemp) > 40) {
                $usernameTemp = substr($usernameTemp, 0, 37) + ' ...';
            }

            $new = [
                'dt'        =>  $this->convDt($record['dt']),
                'username'  =>  $usernameTemp,   
                'userid'    =>  is_null($record['userid']) ? 0 : $record['userid'],   
                'type'      =>  TypeCodes::TYPESTRS_TYPESHORT[$record['type']],
                'matchtype' =>  TypeCodes::TYPESTRS_MT[$record['matchtype']],
                'matchval'  =>  is_null($record['matchval']) ? '' : $record['matchval'],
                'status'    =>  TypeCodes::TYPESTRS_STATUS[$record['status']],
                'ip'        =>  $ip,
                'email'     =>  is_null($record['email']) ? '' : $record['email'],
                'isdummy'   =>  $record['isdummy'],
                'dupcount'  =>  $record['dupcount'],

                'username2'         =>  is_null($record['username']) ? '' : $record['username'],      
                'email2'            =>  is_null($record['email']) ? '' : $record['email'],
                'emaildomain'       =>  $emaildomain,
                'rawemaildomain'    =>  $rawemaildomain,
                'pwd'               =>  is_null($record['pwd']) ? '' : $record['pwd'],
                'info'              =>  is_null($record['info']) ? '' : $record['info'],
                //'blank1'            =>  ' ',

                'comment'           =>  $comment,
                'commentauthorurl'  =>  $record['commentauthorurl'] ?? '', 
                'commentauthordom'  =>  $authorurldomain,
                'commentposttitle'  =>  $record['commentposttitle'] ?? '',
                'commentpostid'     =>  $record['commentpostid'],
                'commentdomains'    =>  $commentDomains,
                //'blank2'            =>  ' ',

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
