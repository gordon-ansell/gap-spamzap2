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
        $banUrl = \admin_url('admin.php') . '?page=spamzap2-add-rule';
        $delUserUrl = \admin_url('users.php') . '?s=';

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

        // Delete user.
        $delUserIcon = new Html('img', [
            'title' => 'Delete user', 
            'alt' => "Delete user icon.", 
            'class' => 'icon deluser',
            'src' => Path::join($iconUrl, 'del-user.png'),
        ]);
        $delUserIconR = $delUserIcon->render();
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

        // Domain ban.
        $domainBanLink = new Html('a', [
            'href' => $banUrl,
            'title' => 'Ban this domain.',
        ]);
        $domainBanIcon = new Html('img', [
            'title' => 'Ban this domain.', 
            'alt' => "Generic block icon, indicating domain to be banned.", 
            'class' => 'icon bandomain',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $domainBanIconR = $domainBanIcon->render();

        // Email ban.
        $emailBanLink = new Html('a', [
            'href' => $banUrl,
            'title' => 'Ban this email address.',
        ]);
        $emailBanIcon = new Html('img', [
            'title' => 'Ban this email address.', 
            'alt' => "Generic block icon, indicating email address to be banned.", 
            'class' => 'icon banemail',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $emailBanIconR = $emailBanIcon->render();

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

            // Domain block (email).
            if ('' != $record['emaildomain'] and 'n/a' != $record['emaildomain']) {
                $domainBanLink->setParam('href', $banUrl . '&domain=' . $record['emaildomain']);
                $record['emaildomain'] = $domainBanLink->render($domainBanIconR) . $record['emaildomain']; 
            }
            if ('' != $record['rawemaildomain'] and 'n/a' != $record['rawemaildomain']) {
                $domainBanLink->setParam('href', $banUrl . '&domain=' . $record['rawemaildomain']);
                $record['rawemaildomain'] = $domainBanLink->render($domainBanIconR) . $record['rawemaildomain']; 
            }

            // Domain block (authorurl).
            if ('' != $record['commentauthordom'] and 'n/a' != $record['commentauthordom']) {
                $domainBanLink->setParam('href', $banUrl . '&domain=' . $record['commentauthordom']);
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
                    $domainBanLink->setParam('href', $banUrl . '&domain=' . $item);
                    $cd .= $domainBanLink->render($domainBanIconR) . $item;
                }
                $record['commentdomains'] = $cd;
            }

            // Email block.
            if ('' != $record['email2'] and 'n/a' != $record['email2']) {
                $emailBanLink->setParam('href', $banUrl . '&email=' . $record['email2']);
                $record['email2'] = $emailBanLink->render($emailBanIconR) . $record['email2']; 
            }

            // User.
            if ('0' != strval($record['userid'])) {
                $tmp = $delUserLink;
                $tmp->setParam('href', $tmp->getParam('href') . strval($record['userid']));
                $record['username2'] = $record['username2'] . $tmp->render($delUserIconR);
            } else {
                $userinfo = \get_user_by('login', strval($record['username2']));
                if (false !== $userinfo) {
                    $tmp = $delUserLink;
                    $tmp->setParam('href', $tmp->getParam('href') . strval($userinfo->ID));
                    $record['username2'] = $record['username2'] . $tmp->render($delUserIconR);
                }
            }

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

            $new = [
                'dt'        =>  $this->convDt($record['dt']),
                'username'  =>  is_null($record['username']) ? '' : $record['username'],   
                'userid'    =>  is_null($record['userid']) ? 0 : $record['userid'],   
                'type'      =>  TypeCodes::TYPESTRS_TYPESHORT[$record['type']],
                'matchtype' =>  TypeCodes::TYPESTRS_MT[$record['matchtype']],
                'matchval'  =>  is_null($record['matchval']) ? '' : $record['matchval'],
                'status'    =>  TypeCodes::TYPESTRS_STATUS[$record['status']],
                'ip'        =>  $ip,
                'email'     =>  is_null($record['email']) ? '' : $record['email'],
                'isdummy'   =>  $record['isdummy'],

                'username2'         =>  is_null($record['username']) ? '' : $record['username'],      
                'email2'            =>  is_null($record['email']) ? '' : $record['email'],
                'emaildomain'       =>  $emaildomain,
                'rawemaildomain'    =>  $rawemaildomain,
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
