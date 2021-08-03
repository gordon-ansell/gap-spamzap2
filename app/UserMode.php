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
namespace App;

use App\Domain\TypeCodes;
use GreenFedora\Wordpress\PluginUser;
use GreenFedora\Wordpress\PluginUserInterface;
use App\Domain\Checker;

/**
 * User-side handling class.
 */
class UserMode extends PluginUser implements PluginUserInterface
{
    /**
     * Checker.
     * @var Checker
     */
    protected $checker = null;

    /**
     * Settings.
     * @var array
     */
    protected $settings = [];
    
    /**
     * Called when plugins loaded (plugins_loaded trigger in WP).
     * 
     * @return  void
     */
    public function pluginsLoaded()
    {
        $dba = $this->app->get('dbaccess');
        $this->settings = $dba->getSettings();

        // Add check to run after registration.
        if ('1' == $this->settings['check-registration']) {
            \add_filter('registration_errors', array($this, 'postRegistrationFilter'), 
                $this->app->getConfig('plugin.priority'), 3);
        }

        // Add a check to run when a comment is posted.
        if ('1' == $this->settings['check-comments']) {
            \add_filter('preprocess_comment', array($this, 'preprocessCommentFilter'), 
                $this->app->getConfig('plugin.priority'), 1);
        }

        // Add a check for contact form 7.
        if ('1' == $this->settings['check-contacts']) {
            \add_filter("wpcf7_before_send_mail", array($this, "preprocessContactForm7Filter"),
                $this->app->getConfig('plugin.priority'), 3);
        }  

        // Add a check for retrieve password.
        if ('1' == $this->settings['check-passwordrecovery']) {
            \add_filter("lostpassword_user_data", array($this, "preprocessRetrievePasswordFilter"),
                $this->app->getConfig('plugin.priority'), 2);
        }  

        // Add a check for failed logins.
        if ('1' == $this->settings['check-login']) {
            \add_action("wp_login_failed", array($this, "preprocessLoginFailedAction"),
                $this->app->getConfig('plugin.priority'), 2);
        }  

        // Add a check for auths.
        if ('1' == $this->settings['check-auths']) {
            \add_filter("authenticate", array($this, "preprocessLoginFilter"),
                $this->app->getConfig('plugin.priority'), 3);
        }  

        $this->checker = new Checker($this->getApp());

    }

    /**
     * Called when a comment is posted.
     * 
     * @param   array   $commentdata    Comment data.
     * 
     * @return  array                   Possibly updates comment data.
     */
    public function preprocessCommentFilter(array $commentdata): array
    {
        $checkBlock = $this->checker->createCheckBlock(TypeCodes::TYPE_COMMENT);

        $checkBlock['username']             = $commentdata['comment_author'];
        $checkBlock['email']                = $commentdata['comment_author_email'];
        $checkBlock['commentauthorurl']     = $commentdata['comment_author_url'];
        $checkBlock['comment']              = $commentdata['comment_content'];
        $checkBlock['userid']               = $commentdata['user_id'];
        $checkBlock['commentpostid']        = $commentdata['comment_post_ID'];

        $cpid = $commentdata['comment_post_ID'];
        if (!empty($cpid) and !is_null($cpid) and 0 != $cpid) {
            $checkBlock['commentposttitle'] = \get_the_title($cpid);
        }

        list($status, $info) = $this->checker->doCheck($checkBlock);

        if ("1" == $this->settings['dummy-mode']) {
            return $commentdata;
        }

        if (false === $status) {
            echo '<pre class="spam">';
            echo "SSSSSSSS   PPPPPPPP      A       M       M" . '<br />' . PHP_EOL;
            echo "S          P      P    A   A     MM     MM" . '<br />' . PHP_EOL;
            echo "SSSSSSSS   PPPPPPPP   AAAAAAA    M M   M M" . '<br />' . PHP_EOL;
            echo "       S   P         A       A   M   M   M" . '<br />' . PHP_EOL;
            echo "SSSSSSSS   P        A         A  M       M" . '<br />' . PHP_EOL;
            echo "</pre>";

            echo '<div><a href="https://en.wikipedia.org/wiki/Forum_spam">More details.</a></div>';
            sleep(3);

            wp_die(__('Suspected spam. The comment has not been posted.'));
        }

        return $commentdata;
    }

    /**
     * Called after a new user tries to register.
     * 
     * @param    \WP_Error   $errors                 Current registration errors.
     * @param    string      $sanitized_user_login   Current user login ID.
     * @param    string      $user_email             Current user email.
     * 
     * @return   \WP_Error                           Registration errors.
     */
    public function postRegistrationFilter(\WP_Error $errors, string $sanitized_user_login, string $user_email): \WP_Error
    {
        $checkBlock = $this->checker->createCheckBlock(TypeCodes::TYPE_REG);

        $checkBlock['username'] = $sanitized_user_login;
        $checkBlock['email'] = $user_email;

        list($status, $info) = $this->checker->doCheck($checkBlock, $errors);

        if ("1" == $this->settings['dummy-mode']) {
            return $errors;
        }

        if (false === $status) {
            $errors->add('email_error', $this->__('<strong>ERROR</strong>: Suspected spammer - go away.'));
        }

        return $errors;
    }

    /**
     * Called when a contact form 7 is submitted.
     * 
     * @param   array   $commentdata    Comment data.
     * 
     * @return  array                   Possibly updates comment data.
     */
    public function preprocessContactForm7Filter($contact_form, &$abort, $submission)
    {
        $checkBlock = $this->checker->createCheckBlock(TypeCodes::TYPE_CONTACT);

        $pd = $submission->get_posted_data();

        $checkBlock['username'] = $pd['your-name'];
        $checkBlock['email'] = $pd['your-email'];
        $checkBlock['comment'] = $pd['your-message'];

        list($status, $info) = $this->checker->doCheck($checkBlock);

        if ("1" == $this->settings['dummy-mode']) {
            return;
        }

        if (false === $status) {
            $submission->set_response($this->__('Suspected spammer, contact form not submitted - go away.'));
            $abort = true;
        }

    }

    /**
     * Called when a retrieve password attempt is submitted.
     * 
     * @param   \WP_User    $user_data     User data.   
     * @param   \WP_Error   $errors        Errors.
     * 
     * @return  \WP_User                   User data.
     */
    public function preprocessRetrievePasswordFilter(\WP_User $user_data, \WP_Error $errors)
    {
        $checkBlock = $this->checker->createCheckBlock(TypeCodes::TYPE_LOSTPASSWORD);

        $checkBlock['username'] = $user_data->user_login;
        $checkBlock['userid'] = $user_data->ID;
        $checkBlock['email'] = $user_data->user_email;
        $checkBlock['commentauthorurl'] = $user_data->user_url;

        list($status, $info) = $this->checker->doCheck($checkBlock, $errors);

        if ("1" == $this->settings['dummy-mode']) {
            return;
        }

        if (false === $status) {
            $errors->add('email_error', $this->__('<strong>ERROR</strong>: Suspected hacker - go away.'));
        }

        return $user_data;
    }

    /**
     * Called when a login attempt fails.
     * 
     * @param   string                      $username   Username.
     * @param   \WP_Error                   $error      Error message.
     * 
     * @return  
     */
    public function preprocessLoginFailedAction(string $username, \WP_Error $error)
    {
        $data = $this->checker->createCheckBlock(TypeCodes::TYPE_LOGIN);
        $data['matchtype'] = TypeCodes::MT_LOGIN_ERROR;
        $data['matchval'] = strip_tags($error->get_error_message(), '<strong>');
        $data['dt'] = $this->getDt();
        $data['status'] = TypeCodes::STATUS_ERROR;
        $data['username'] = $username;
        $lm = $this->getApp()->get('logmodel');
        $lm->create($data);

        \status_header(401);
        \nocache_headers();
    }

    /**
     * Called when a login attempt is submitted.
     * 
     * @param   null|\WP_User|\WP_Error     $user       User data or errors.   
     * @param   string                      $username   Username.
     * @param   string                      $password   Password.
     * 
     * @return  \WP_User                   User data.
     */
    public function preprocessLoginFilter($user, string $username, string $password)
    {         
        if (!isset($_GET['loggedout'])) {
            $checkBlock = $this->checker->createCheckBlock(TypeCodes::TYPE_LOGIN);
            $checkBlock['username'] = $username;
            list($status, $info) = $this->checker->doCheck($checkBlock);
            if (false === $status) {
                \wp_die('Suspected trouble maker - go away');
            } else {
                $data = $this->checker->createCheckBlock(TypeCodes::TYPE_LOGIN);
                $data['matchtype'] = TypeCodes::MT_LOGIN_AUTH;
                $data['matchval'] = $username;
                $data['dt'] = $this->getDt();
                $data['status'] = TypeCodes::STATUS_ALLOW;

                $secret_key = $this->settings['secret-key'];
                $secret_iv = $this->settings['secret-iv'];
                if (!empty($secret_key) and !empty($secret_iv)) {
                    $lm = $this->getApp()->get('logmodel');
                    $data['info'] = $lm->cryptic($password, $secret_key, $secret_iv);
                }
                $lm->create($data);    
            }

        }

        return $user;

    }
}
