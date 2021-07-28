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
namespace App\PageProcessor;

use GreenFedora\Wordpress\PageProcessor\AbstractPageProcessor;
use GreenFedora\Wordpress\PageProcessor\PageProcessorInterface;
use GreenFedora\Validator\IPAddressPossibleCIDRValidator;
use GreenFedora\Form\Form;
use GreenFedora\Form\FormInterface;
use GreenFedora\Stdlib\Arr\Arr;
use App\Domain\TypeCodes;
use GreenFedora\IP\IPAddress;
use GreenFedora\Stdlib\Path;

/**
 * Add rule page processor.
 */
class AddRulePageProcessor extends AbstractPageProcessor implements PageProcessorInterface
{
    /**
     * Get the form defaults.
     * 
     * @return array
     */
    protected function getFormDefaults(): array
    {
        return array(
            'domain-block' => '', 
            'isdomainregex' => 'no',
            'ip-block' => '',
            'email-block' => '',
            'isemailregex' => 'no',
            'string-block' => '',
            'isstringregex' => 'no',
            'applyto_user' => "on",
            'applyto_comment' => "on",
            'ip-allow' => '',
        );        
    }

    /**
     * Create the add rule form.
     * 
     * @return  FormInterface 
     */
    protected function createAddRuleForm(): FormInterface
    {
        //$ph = new FormPersistHandler($this->parent->getApp()->get('session'), $this->getFormDefaults(), 'add-block');

        $form = new Form('add-rule', '');
        $form->setAutoWrap('fieldset');
        $form->setCsrf(false);

        $form->addField('errors', ['name' => 'sz-errors', 'class' => 'formerror']);

        // ==========================

        // BLOCKS
        $form->addField('divopen', ['name' => 'blocks', 'class' => 'sect']);

            // Row one.
            $form->addField('divopen', ['name' => 'row1', 'class' => 'three-columns']);

                $form->addField('inputtext', ['name' => 'ip-block', 'label' => 'Block IP Address', 
                    'placeholder' => '192.168.0.0/16', 'title' => "Enter an IP address or CIDR to block.", 'style' => 'width: 10em'])
                    ->addValidator(new IPAddressPossibleCIDRValidator(['IP block']));

            $form->addField('divclose', ['name' => 'row1close']);

            // Row two.
            $form->addField('divopen', ['name' => 'row2', 'class' => 'three-columns']);

                $form->addField('inputtext', ['name' => 'domain-block', 'label' => 'Domain Name', 
                    'placeholder' => 'example.com', 'title' => "Enter a domain name to block. Applies to author URLs, email addresses and comments.", 
                    'style' => 'width: 20em']);

                $form->addField('radioset', ['name' => 'isdomainregex', 'label' => 'Is Domain Block Regex?', 'class' => 'radio', 
                    'options' => ['yes' => 'Yes', 'no' => 'No'], 'style' => 'width: 10em',
                    'title' => "Do you want to use this domain block as a regular expression?"]);

            $form->addField('divclose', ['name' => 'row2close']);

            // Row three.
            $form->addField('divopen', ['name' => 'row3', 'class' => 'three-columns']);

                $form->addField('inputtext', ['name' => 'email-block', 'label' => 'Email Address', 
                    'placeholder' => 'someone@example.com', 'title' => "Enter an email address to block.", 'style' => 'width: 20em']);


                $form->addField('radioset', ['name' => 'isemailregex', 'label' => 'Is Email Block Regex?', 'class' => 'radio', 
                    'options' => ['yes' => 'Yes', 'no' => 'No'], 'style' => 'width: 10em',
                    'title' => "Do you want to use this email block as a regular expression?"]);

            $form->addField('divclose', ['name' => 'row3close']);

            // Row four.
            $form->addField('divopen', ['name' => 'row4', 'class' => 'three-columns']);

                $form->addField('inputtext', ['name' => 'string-block', 'label' => 'String', 
                    'placeholder' => '', 'title' => "Enter a string or regex to block.", 'style' => 'width: 20em']);

                $form->addField('radioset', ['name' => 'isstringregex', 'label' => 'Is String Block Regex?', 'class' => 'radio', 
                    'options' => ['yes' => 'Yes', 'no' => 'No'], 'style' => 'width: 10em',
                    'title' => "Do you want to use this string block as a regular expression?"]);
        
                $form->addField('spanopen', ['name' => 'cbset', 'class' => 'cbset']);

                    $form->addField('label', ['name'=> 'applyto-label', 'value' => "String Block Applies To", 'class' => 'hdr']);
                    $form->addField('checkbox', ['name' => 'applyto_user', 'label' => 'Users', 'class' => 'checkbox']);
                    $form->addField('checkbox', ['name' => 'applyto_comment', 'label' => 'Comments', 'class' => 'checkbox']);

                $form->addField('spanclose', ['name' => 'cbsetclose']);

            $form->addField('divclose', ['name' => 'row4close']);

        $form->addField('divclose', ['name' => 'blocksclose']);


        // ==========================

        // ALLOWS
        $form->addField('divopen', ['name' => 'allows', 'class' => 'sect']);

            $form->addField('inputtext', ['name' => 'ip-allow', 'label' => 'Allow IP Address', 
                'placeholder' => '192.168.0.0/16', 'title' => "Enter an IP address to allow.", 'style' => 'width: 10em']);

        $form->addField('divclose', ['name' => 'allowsclose']);

        // ==========================

        // End stuff.
        $form->addField('buttonsubmit', ['name' => 'submit', 'value' => 'Submit', 'style' => 'width: 10em']);
        //$form->addField('inputhidden', ['name' => $this->parent->pref('form-hidden'), 'value' => 'Y']);

        $form->addRawField(\wp_nonce_field('add-rule'));

        $form->setAutofocus('ip-block');

        return $form;
         
    }

    /**
     * Process.
     * 
     * @return
     */
    public function process()
    {
        $dt = $this->getDt();
        $logUrl = \admin_url('admin.php') . '?page=spamzap2';

        if (isset($_GET['ip'])) {
            $validator = new IPAddressPossibleCIDRValidator([], null, null, false);
            if ($validator->validate($_GET['ip'])) {
                list($m, $e) = $this->addIPBlock($dt, $_GET['ip']);
                $_SESSION['sz2-m'] = $m;
                $_SESSION['sz2-e'] = $e;
            } else {
                unset($_SESSION['sz2-m']);
                $_SESSION['sz2-e'] = ["IP address invalid."];
            }
            echo("<script>location.href = '" . $logUrl . "'</script>");
           return;
        } else if (isset($_GET['domain'])) {
            list($m, $e) = $this->addDomainBlock($dt, $_GET['domain'], 'no');
            $_SESSION['sz2-m'] = $m;
            $_SESSION['sz2-e'] = $e;
            echo("<script>location.href = '" . $logUrl . "'</script>");
            return;
        } else if (isset($_GET['email'])) {
            list($m, $e) = $this->addEmailBlock($dt, $_GET['email'], 'no');
            $_SESSION['sz2-m'] = $m;
            $_SESSION['sz2-e'] = $e;
            echo("<script>location.href = '" . $logUrl . "'</script>");
            return;
        }

        $defs = Arr::fromArray($this->getFormDefaults());
        $form = $this->createAddRuleForm()->load($defs);

        $msgs = [];
        $errors = [];

        //var_dump($_REQUEST);
        //throw new \Exception('123');

        if (isset($_POST['form-submitted']) and $_POST['form-submitted'] === 'add-rule') {
            if (!\wp_verify_nonce($_REQUEST['_wpnonce'], 'add-rule')) {
                wp_die('Security error');
            }
            if ($form->validate($_POST)) {
                $ipBlock = $_POST['ip-block'];
                $ipAllow = $_POST['ip-allow'];

                $domainBlock = $_POST['domain-block'];
                $isdomainregex = $_POST['isdomainregex'];

                $emailBlock = $_POST['email-block'];
                $isemailregex = $_POST['isemailregex'];

                $stringBlock = $_POST['string-block'];
                $isstringregex = $_POST['isstringregex'];
                $applytouser = $_POST['applyto_user'];
                $applytocomment = $_POST['applyto_comment'];

                if (empty($domainBlock) and empty($ipBlock) and empty($emailBlock) and empty($stringBlock) and empty($ipAllow)) {
                    $form->addError("You must select something to do.");
                } else {

                    // Domain block.
                    if (!empty($domainBlock)) {
                        list($m, $e) = $this->addDomainBlock($dt, $domainBlock, $isdomainregex);
                        if (!is_null($m)) $msgs[] = $m;
                        if (!is_null($e)) $errors[] = $e;
                    }

                    // Email block.
                    if (!empty($emailBlock)) {
                        list($m, $e) = $this->addEmailBlock($dt, $emailBlock, $isemailregex);
                        if (!is_null($m)) $msgs[] = $m;
                        if (!is_null($e)) $errors[] = $e;
                    }

                    // String block.
                    if (!empty($stringBlock)) {
                        $atu = ($applytouser) ? 1 : 0;
                        $atc = ($applytocomment) ? 1 : 0;
                        list($m, $e) = $this->addStringBlock($dt, $stringBlock, $isstringregex, $atc, $atu);
                        if (!is_null($m)) $msgs[] = $m;
                        if (!is_null($e)) $errors[] = $e;
                    }

                    // IP block.
                    if (!empty($ipBlock)) {
                        list($m, $e) = $this->addIPBlock($dt, $ipBlock);
                        if (!is_null($m)) $msgs[] = $m;
                        if (!is_null($e)) $errors[] = $e;
                    }

                    // IP allow.
                    if (!empty($ipAllow)) {
                        list($m, $e) = $this->addIPAllow($dt, $ipAllow);
                        if (!is_null($m)) $msgs[] = $m;
                        if (!is_null($e)) $errors[] = $e;
                    }

                    //var_dump($_POST);
                    //\wp_die('validated', 'post', $_POST);
                }
            }
        }

        $tplData = [
            'addRuleForm'   =>  $form,
            'msgs'          =>  $msgs,
            'errors'        =>  $errors,
        ];

        $template = $this->parent->getApp()->get('template');

        echo $template->render('adminAddRule', $tplData);

    }

    /**
     * Add a string block.
     * 
     * @param   string      $dt             Date/Time.
     * @param   string      $stringBlock    String to block.
     * @param   string      $isregex        Regex?
     * @param   string      $comment        Apply to comment?
     * @param   string      $user           Apply to user?
     * 
     * @return  array                       (msg, error)
     */
    protected function addstringBlock(string $dt, string $stringBlock, string $isregex, int $comment, int $user): array
    {
        $msg = null;
        $error = null;

        $dbm = $this->parent->getApp()->get('stringblockmodel');
        if ($dbm->hasValue($stringBlock)) {
            $error = sprintf("We are already blocking string '%s'.", stripslashes($stringBlock));
        } else if (1 !== $comment and 1 !== $user) { 
            $error = sprintf("String block neither applies to comments or usernames. Ignored.");
        } else {
            $ir = ('yes' == $isregex) ? 1 : 0;
            $dbm->create(['item' => $stringBlock, 'dt' => $dt, 'isregex' => $ir, 'comment' => $comment, 'username' => $user]);
            $lm = $this->parent->getApp()->get('logmodel');
            $rec = [
                'type' => TypeCodes::TYPE_INFO,
                'matchtype' => TypeCodes::MT_NEW_RULE, 
                'matchval' => 'String: ' . stripslashes($stringBlock),
                'dt' => $dt,
                'status' => TypeCodes::STATUS_INFO,
            ];
            $lm->create($rec);
            $msg = sprintf("Added string block for '%s'.", stripslashes($stringBlock)); 
        }

        return [$msg, $error];
    }

    /**
     * Add an email block.
     * 
     * @param   string      $dt             Date/Time.
     * @param   string      $emailBlock     Email to block.
     * @param   string      $isregex        Regex?
     * 
     * @return  array                       (msg, error)
     */
    protected function addEmailBlock(string $dt, string $emailBlock, string $isregex): array
    {
        $msg = null;
        $error = null;

        $dbm = $this->parent->getApp()->get('emailblockmodel');
        if ($dbm->hasValue($emailBlock)) {
            $error = sprintf("We are already blocking email '%s'.", stripslashes($emailBlock));
        } else {
            $ir = ('yes' == $isregex) ? 1 : 0;
            $dbm->create(['item' => $emailBlock, 'dt' => $dt, 'isregex' => $ir]);
            $lm = $this->parent->getApp()->get('logmodel');
            $rec = [
                'type' => TypeCodes::TYPE_INFO,
                'matchtype' => TypeCodes::MT_NEW_RULE, 
                'matchval' => 'Email: ' . stripslashes($emailBlock),
                'dt' => $dt,
                'status' => TypeCodes::STATUS_INFO,
            ];
            $lm->create($rec);
            $msg = sprintf("Added email block for '%s'.", stripslashes($emailBlock)); 
        }

        return [$msg, $error];
    }

    /**
     * Add a domain block.
     * 
     * @param   string      $dt             Date/Time.
     * @param   string      $domainBlock    Domain to block.
     * @param   string      $isregex        Regex?
     * 
     * @return  array                       (msg, error)
     */
    protected function addDomainBlock(string $dt, string $domainBlock, string $isregex): array
    {
        $msg = null;
        $error = null;

        $dbm = $this->parent->getApp()->get('domainblockmodel');
        if ($dbm->hasValue($domainBlock)) {
            $error = sprintf("We are already blocking domain '%s'.", stripslashes($domainBlock));
        } else {
            $ir = ('yes' == $isregex) ? 1 : 0;
            $dbm->create(['item' => $domainBlock, 'dt' => $dt, 'isregex' => $ir]);
            $lm = $this->parent->getApp()->get('logmodel');
            $rec = [
                'type' => TypeCodes::TYPE_INFO,
                'matchtype' => TypeCodes::MT_NEW_RULE, 
                'matchval' => 'Domain: ' . stripslashes($domainBlock),
                'dt' => $dt,
                'status' => TypeCodes::STATUS_INFO,
            ];
            $lm->create($rec);
            $msg = sprintf("Added domain block for '%s'.", stripslashes($domainBlock)); 
        }

        return [$msg, $error];
    }

    /**
     * Add an IP block.
     * 
     * @param   string      $dt             Date/Time.
     * @param   string      $ipBlock        IP to block.
     * 
     * @return  array                       (msg, error)
     */
    protected function addIPBlock(string $dt, string $ipBlock): array
    {
        $msg = null;
        $error = null;

        $ipbm = $this->parent->getApp()->get('ipblockmodel');
        $iscovered = $ipbm->isCovered($ipBlock);
        $isoverriding = $ipbm->isOverriding($ipBlock);


        if (!is_null($iscovered)) {
            $error = sprintf("We are already blocking IP '%s' via: %s.", $ipBlock, $iscovered);
        } else {
            $ipbm->create(['ip' => $ipBlock, 'dt' => $dt]);
            $lm = $this->parent->getApp()->get('logmodel');
            $rec = [
                'type' => TypeCodes::TYPE_INFO,
                'matchtype' => TypeCodes::MT_NEW_RULE, 
                'matchval' => 'IP Block: ' . $ipBlock,
                'dt' => $dt,
                'status' => TypeCodes::STATUS_INFO,
            ];
            $lm->create($rec);
            $msg = sprintf("Added IP block for '%s'.", $ipBlock); 
            if (!is_null($isoverriding)) {
                $or = [];
                foreach ($isoverriding as $single) {
                    $or[] = sprintf("%s from %s (%s).", $single[1], $this->convDt($single[2]), $single[0]);
                }
                $msg .= "<br />Above entry overrides: <br />" . implode("<br />", $or);
                $ipbm->delete($single[0]);
            }
        }

        return [$msg, $error];
    }

    /**
     * Add an IP allow.
     * 
     * @param   string      $dt             Date/Time.
     * @param   string      $ipBlock        IP to allow.
     * 
     * @return  array                       (msg, error)
     */
    protected function addIPAllow(string $dt, string $ipAllow): array
    {
        $msg = null;
        $error = null;

        $ipam = $this->parent->getApp()->get('ipallowmodel');
        $iscovered = $ipam->isCovered($ipAllow);
        $isoverriding = $ipam->isOverriding($ipAllow);


        if (!is_null($iscovered)) {
            $error = sprintf("We are already allowing IP '%s' via: %s.", $ipAllow, $iscovered);
        } else {
            $ipam->create(['ip' => $ipAllow, 'dt' => $dt]);
            $lm = $this->parent->getApp()->get('logmodel');
            $rec = [
                'type' => TypeCodes::TYPE_INFO,
                'matchtype' => TypeCodes::MT_NEW_RULE, 
                'matchval' => 'IP Allow: ' . $ipAllow,
                'dt' => $dt,
                'status' => TypeCodes::STATUS_INFO,
            ];
            $lm->create($rec);
            $msg = sprintf("Added IP allow for '%s'.", $ipAllow); 
            if (!is_null($isoverriding)) {
                $or = [];
                foreach ($isoverriding as $single) {
                    $or[] = sprintf("%s from %s (%s).", $single[1], $this->convDt($single[2]), $single[0]);
                }
                $msg .= "<br />Above entry overrides: <br />" . implode("<br />", $or);
                $ipam->delete($single[0]);
            }
        }

        return [$msg, $error];
    }
}