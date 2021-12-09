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
namespace App\Domain;

use App\Domain\TypeCodes;
use GreenFedora\IP\IPAddress;
use GreenFedora\Wordpress\WordpressApplicationInterface;
use GreenFedora\Stdlib\Domain;

/**
 * Checker class.
 */
class Checker
{
    /**
     * App.
     * @var WordpressApplicationInterface
     */
    protected $app = null;

    /**
     * Constructor.
     * 
     * @param   WordpressApplicationInterface   $app    Application.
     * @return  void
     */
    public function __construct(WordpressApplicationInterface $app)
    {
        $this->app = $app;
    }

    /**
     * Get the application.
     * 
     * @return WordpressApplicationInterface
     */
    protected function getApp(): WordpressApplicationInterface
    {
        return $this->app;
    }

    /**
     * Get the datetime.
     * 
     * @param
     * @return  string
     */
    protected function getDt(): string
    {
        $dt = new \DateTime("now", new \DateTimeZone('UTC'));
        return $dt->format(\DateTimeInterface::ATOM);        
    }

    /**
     * Get a data field.
     * 
     * @param   string  $field      Field to get.
     * @param   array   $data       Data block.
     * @return  string|null    
     */
    protected function getDf(string $field, array $data): ?string
    {
        if (
            'commentauthorurl' == $field and TypeCodes::TYPE_COMMENT != $data['type'] 
            and TypeCodes::TYPE_LOSTPASSWORD != $data['type']
            and TypeCodes::TYPE_LOGIN != $data['type']
        ) {
            return null;
        }

        if (
            'comment' == $field and 
                (
                    TypeCodes::TYPE_REG == $data['type']
                    or TypeCodes::TYPE_LOSTPASSWORD == $data['type']
                    or TypeCodes::TYPE_LOGIN == $data['type']
                )
        ) {
            return null;
        }

        if (array_key_exists($field, $data) and !is_null($data[$field]) and !empty($data[$field]) and 'n/a' != $data[$field]) {
            return stripslashes($data[$field]);
        }
        return null;
    }

    /**
     * Create the check block.
     * 
     * @param   string  $type   Type.
     * @return  array
     */
    public function createCheckBlock(int $type): array
    {
        $settings = $this->getApp()->get('dbaccess')->getSettings();
        $dummy = ("1" == $settings['dummy-mode']) ? 1 : 0;

        $cfg = $this->getApp()->getConfig('plugin');
        $ip = IPAddress::getClientIp();

        if (true === $cfg['usedefip'] and ('::1' == $ip or 'localhost' == $ip or '127.0.0.1' == $ip)) {
            $ip = $cfg['defaultip'];
        }

        $checkBlock = [
            'type'                  =>  $type,
            'ip'                    =>  $ip,
            'username'              =>  null,
            'userid'                =>  null,
            'pwd'                   =>  null,
            'email'                 =>  null,
            'commentauthorurl'      =>  null,
            'isdummy'               =>  $dummy,
        ];

        return $checkBlock;
    }

    /**
     * Run the checks on a given data block.
     * 
     * @param   array       $data       Data block.
     * @param   \WP_Error   $errors     Registration errors.
     * @return  array                   (status, info).
     */
    public function doCheck(array $data, ?\WP_Error &$errors = null): array
    {
        // Load the settings.
        $settingsmodel = $this->getApp()->get('settingsmodel');
        $settings = $this->getApp()->get('dbaccess')->getSettings();
 
        // Load some database stuff.
        $lm = $this->getApp()->get('logmodel');
        $ipallowmodel = $this->getApp()->get('ipallowmodel');
        $ipblockmodel = $this->getApp()->get('ipblockmodel');
        $iptempblockmodel = $this->getApp()->get('iptempblockmodel');
        $domainblockmodel = $this->getApp()->get('domainblockmodel');
        $emailblockmodel = $this->getApp()->get('emailblockmodel');
        $stringblockmodel = $this->getApp()->get('stringblockmodel');

        // Lookup the IP.
        $lookupModel = $this->getApp()->get('iplookupmodel');
        $iplData = $lookupModel->addLookup($data['ip'], false, true);

        // --------------------------------------------------------------------------
        // Is block all set?
        // --------------------------------------------------------------------------
        if ('1' == $settings['block-all'] and !\current_user_can('manage_options')) {
            $logData = $data;
            $logData['matchtype']   = TypeCodes::MT_BLOCK_ALL;
            $logData['matchval'] = '';
            $logData['dt'] = $this->getDt();
            $logData['status'] = TypeCodes::STATUS_BLOCK;
            $lm->create($logData);
            return [false, null];
        }

        // --------------------------------------------------------------------------
        // If this is a registration, login or lost password, check for errors first.
        // --------------------------------------------------------------------------
        if ((TypeCodes::TYPE_REG == $data['type'] or TypeCodes::TYPE_LOSTPASSWORD == $data['type'] or TypeCodes::TYPE_LOGIN == $data['type']) 
            and ($errors instanceof \WP_Error) and $errors->has_errors()) {

            $code = TypeCodes::MT_REG_ERROR;
            if (TypeCodes::TYPE_LOSTPASSWORD == $data['type']) {
                $code = TypeCodes::MT_LP_ERROR;
            } else if (TypeCodes::TYPE_LOGIN == $data['type']) {
                $code = TypeCodes::MT_LOGIN_ERROR;
            }

            $logData = $data;
            $logData['matchtype'] = $code;
            $logData['matchval'] = $errors->get_error_message();
            $logData['dt'] = $this->getDt();
            $logData['status'] = TypeCodes::STATUS_ERROR;
            $lm->create($logData);
            return [true, null];
        }

        // --------------------------------------------------------------------------
        // First see if we're ignoring checks for logged in users.
        // --------------------------------------------------------------------------
        if (TypeCodes::TYPE_REG != $data['type'] and TypeCodes::TYPE_LOGIN != $data['type'] 
            and "1" == $settings['ignore-if-logged-in'] and !empty($data['userid'])) {
            $logData = $data;
            $logData['matchtype']   = TypeCodes::MT_LOGGED_IN;
            $logData['dt'] = $this->getDt();
            $logData['status'] = TypeCodes::STATUS_ALLOW;
            $lm->create($logData);
            return [true, null];
        }

        // --------------------------------------------------------------------------
        // Next do an IP allow check.
        // --------------------------------------------------------------------------
        $isIPAllowed = $ipallowmodel->isCovered($data['ip'], true);
        if (!is_null($isIPAllowed)) {
            $logData = $data;
            $logData['matchtype']   = TypeCodes::MT_IP_ALLOW;
            $logData['matchval'] = $isIPAllowed;
            $logData['dt'] = $this->getDt();
            $logData['status'] = TypeCodes::STATUS_ALLOW;
            $lm->create($logData);
            return [true, null];
        }

        // --------------------------------------------------------------------------
        // Next do an IP block check.
        // --------------------------------------------------------------------------
        $isIPBlocked = $ipblockmodel->isCovered($data['ip'], true);
        if (!is_null($isIPBlocked)) {
            $logData = $data;
            $logData['matchtype']   = TypeCodes::MT_IP_BLOCK;
            $logData['matchval'] = $isIPBlocked;
            $logData['dt'] = $this->getDt();
            $logData['status'] = TypeCodes::STATUS_BLOCK;
            $lm->create($logData);
            return [false, null];
        }

        // --------------------------------------------------------------------------
        // Next do an IP TEMP block check.
        // --------------------------------------------------------------------------
        $isIPTempBlocked = $iptempblockmodel->isCovered($data['ip'], true);
        if (!is_null($isIPTempBlocked)) {
            $logData = $data;
            $logData['matchtype']   = TypeCodes::MT_IP_TEMP_BLOCK;
            $logData['matchval'] = $isIPTempBlocked;
            $logData['dt'] = $this->getDt();
            $logData['status'] = TypeCodes::STATUS_BLOCK;
            $lm->create($logData);
            return [false, null];
        }

        // --------------------------------------------------------------------------
        // Next do a domain block check.
        // --------------------------------------------------------------------------
        // Simple extractions.
        $authorurl = $this->getDf('commentauthorurl', $data);
        $emailaddress = $this->getDf('email', $data);
        $username = $this->getDf('username', $data);
        $emaildomain = null;
        if (!is_null($emailaddress)) {
            $sp = explode('@', $emailaddress);
            $emaildomain = $sp[1];
        }

        // Domains in comments.
        $comment = $this->getDf('comment', $data);
        $commentDomains = [];
        if (!is_null($comment)) {
            $commentDomains = Domain::domainsFromString($comment);
            $commentDomains = array_unique($commentDomains);
        }

        // Load the domains.
        $domainsToCheck = $domainblockmodel->listAll();

        // Loop through.
        foreach ($domainsToCheck as $record) {

            // ------- Author URL.
            if (!is_null($authorurl)) {
                if ('1' === $record['isregex']) {
                    $re = '~' . stripslashes($record['item']) . '~i';
                    if (1 === preg_match($re, $authorurl)) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_DOM_AURL;
                        $logData['matchval'] = stripslashes($record['item']);
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                } else {
                    if (false !== stripos($authorurl, $record['item'])) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_DOM_AURL;
                        $logData['matchval'] = $record['item'];
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                }
            }

            // ------- Email.
            if (!is_null($emaildomain)) {
                if ('1' === $record['isregex']) {
                    $re = '~' . stripslashes($record['item']) . '~i';
                    if (1 === preg_match($re, $emaildomain)) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_DOM_EMAIL;
                        $logData['matchval'] = stripslashes($record['item']);
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                } else {
                    if (false !== stripos($emaildomain, $record['item'])) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_DOM_EMAIL;
                        $logData['matchval'] = $record['item'];
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                }
            }

            // ------- Comment.
            if (!is_null($comment)) {
                foreach ($commentDomains as $singleDomain) {
                    if ('1' === $record['isregex']) {
                        $re = '~' . stripslashes($record['item']) . '~i';
                        if (1 === preg_match($re, $singleDomain)) {
                            $logData = $data;
                            $logData['matchtype']   = TypeCodes::MT_DOM_COMMENT;
                            $logData['matchval'] = stripslashes($record['item']);
                            $logData['dt'] = $this->getDt();
                            $logData['status'] = TypeCodes::STATUS_BLOCK;
                            $logData['commentdomains'] = implode(',',$commentDomains);
                            $lm->create($logData);
                            return [false, null];
                        }
                    } else {
                        if (false !== stripos($singleDomain, $record['item'])) {
                            $logData = $data;
                            $logData['matchtype']   = TypeCodes::MT_DOM_COMMENT;
                            $logData['matchval'] = $record['item'];
                            $logData['dt'] = $this->getDt();
                            $logData['status'] = TypeCodes::STATUS_BLOCK;
                            $logData['commentdomains'] = implode(',',$commentDomains);
                            $lm->create($logData);
                            return [false, null];
                        }
                    }
                }

                // Do an extra check on contacts.
                if (TypeCodes::TYPE_CONTACT == $data['type']) {
                    if (false !== stripos($comment, $record['item'])) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_DOM_COMMENT;
                        $logData['matchval'] = $record['item'];
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $cmtd = implode(',',$commentDomains);
                        if ('' != $cmtd) {
                            $cmtd .= ',' . $record['item'];
                        } else {
                            $cmtd = $record['item'];
                        }
                        $logData['commentdomains'] = $cmtd;
                        $lm->create($logData);
                        return [false, null];
                    }
                }
            }

            // ------- IP domain.
            if (!empty($iplData['ipl_domain'])) {
                if ('1' === $record['isregex']) {
                    $re = '~' . stripslashes($record['item']) . '~i';
                    if (1 === preg_match($re, $iplData['ipl_domain'])) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_DOM_IP;
                        $logData['matchval'] = stripslashes($record['item']);
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                } else {
                    if (false !== stripos($iplData['ipl_domain'], $record['item'])) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_DOM_IP;
                        $logData['matchval'] = $record['item'];
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                }
            }
        }

        // --------------------------------------------------------------------------
        // Next do a email block check.
        // --------------------------------------------------------------------------

        // Load the emails.
        $emailsToCheck = $emailblockmodel->listAll();

        // Loop through.
        foreach ($emailsToCheck as $record) {
            if (!is_null($emailaddress)) {
                if ('1' === $record['isregex']) {
                    $re = '~' . stripslashes($record['item']) . '~i';
                    if (1 === preg_match($re, $emailaddress)) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_EMAIL_BLOCK;
                        $logData['matchval'] = stripslashes($record['item']);
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                } else {
                    if (false !== stripos($emailaddress, $record['item'])) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_EMAIL_BLOCK;
                        $logData['matchval'] = $record['item'];
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                }
            }
        }
 
        // --------------------------------------------------------------------------
        // Next do a string block check.
        // --------------------------------------------------------------------------

        // Load the strings.
        $stringsToCheck = $stringblockmodel->listAll();

        // Loop through.
        foreach ($stringsToCheck as $record) {

            // ------- Comment.
            if (!is_null($comment) and "1" == $record['comment']) {
                if ('1' === $record['isregex']) {
                    $re = '~' . stripslashes($record['item']) . '~i';
                    if (1 === preg_match($re, $comment)) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_STRING_BLOCK_COM;
                        $logData['matchval'] = stripslashes($record['item']);
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                } else {
                    if (false !== stripos($comment, $record['item'])) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_STRING_BLOCK_COM;
                        $logData['matchval'] = $record['item'];
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                }
            }

            // ------- Username.
            if (!is_null($username) and "1" == $record['username']) {
                if ('1' === $record['isregex']) {
                    $re = '~' . stripslashes($record['item']) . '~i';
                    if (1 === preg_match($re, $username)) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_STRING_BLOCK_USER;
                        $logData['matchval'] = stripslashes($record['item']);
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                } else {
                    if (false !== stripos($username, $record['item'])) {
                        $logData = $data;
                        $logData['matchtype']   = TypeCodes::MT_STRING_BLOCK_USER;
                        $logData['matchval'] = $record['item'];
                        $logData['dt'] = $this->getDt();
                        $logData['status'] = TypeCodes::STATUS_BLOCK;
                        $lm->create($logData);
                        return [false, null];
                    }
                }
            }
        }

        // --------------------------------------------------------------------------
        // If nothing has been blocked we just allow it.
        // --------------------------------------------------------------------------


        if (!is_null($username) and (is_null($data['userid']) or "0" == $data['userid'])) {
            $usrInfo = \get_user_by('login', $username);
            if (false !== $usrInfo) {
                $data['userid'] = $usrInfo->ID;
            }
        }

        if (TypeCodes::TYPE_LOGIN != $data['type']) {
            $logData = $data;
            $logData['matchtype']   = TypeCodes::MT_PASSED;
            $logData['dt'] = $this->getDt();
            $logData['status'] = TypeCodes::STATUS_ALLOW;
            $lm->create($logData);
        }
        
        return [true, null];
    }
}
