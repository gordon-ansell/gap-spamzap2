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
use GreenFedora\Form\Form;
use GreenFedora\Form\FormInterface;
use GreenFedora\Stdlib\Arr\Arr;
use GreenFedora\Validator\NumericBetweenValidator;

/**
 * Options page processor.
 */
class OptionsPageProcessor extends AbstractPageProcessor implements PageProcessorInterface
{
    /**
     * Get the form defaults.
     * 
     * @return array
     */
    protected function getFormDefaults(): array
    {
        $settings = $this->parent->getApp()->get('dbaccess')->getSettings(true);
        unset($settings['log-count']);
        unset($settings['manage-rules-sel']);
        return $settings;

        /*
        return array(
            'check-comments' => $settings['check-comments'], 
            'check-registration' => $settings['check-registration'], 
            'check-contacts' => $settings['check-contacts'], 
            'log-lines' => $settings['log-lines'],
            'ip-block' => '',
            'email-block' => '',
            'isemailregex' => 'no',
            'string-block' => '',
            'isstringregex' => 'no',
            'applyto_user' => "on",
            'applyto_comment' => "on",
            'ip-allow' => '',
        );
        */        
    }

    /**
     * Create the options form.
     * 
     * @return  FormInterface 
     */
    protected function createOptionsForm(): FormInterface
    {
        //$ph = new FormPersistHandler($this->parent->getApp()->get('session'), $this->getFormDefaults(), 'add-block');

        $form = new Form('options', '');
        $form->setAutoWrap('fieldset');
        $form->setCsrf(false);

        $form->addField('errors', ['name' => 'sz-errors', 'class' => 'formerror']);

        // ==========================


        // Row one.
        $form->addField('divopen', ['name' => 'row1', 'class' => 'three-columns']);

            $form->addField('radioset', ['name' => 'check-comments', 'label' => 'Check Comments?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Do you want to process all comments through SpamZap2?"]);

            $form->addField('radioset', ['name' => 'check-registration', 'label' => 'Check Registration?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Do you want to process all registrations through SpamZap2?"]);

            $form->addField('radioset', ['name' => 'check-contacts', 'label' => 'Check Contacts?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Do you want to process all Contact Form 7 submissions through SpamZap2?"]);

        $form->addField('divclose', ['name' => 'row1close']);

        // Row two.
        $form->addField('divopen', ['name' => 'row2', 'class' => 'three-columns']);

            $form->addField('radioset', ['name' => 'check-passwordrecovery', 'label' => 'Check Password Recovery?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Do you want to process password recovery attempts through SpamZap2?"]);

            $form->addField('radioset', ['name' => 'check-login', 'label' => 'Check Logins?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Do you want to process login attempts through SpamZap2?"]);

            $form->addField('radioset', ['name' => 'check-auths', 'label' => 'Check Authentications?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Do you want to process authentication attempts through SpamZap2?"]);

        $form->addField('divclose', ['name' => 'row2close']);

        // Row three.
        $form->addField('divopen', ['name' => 'row3', 'class' => 'three-columns']);

            $form->addField('radioset', ['name' => 'ignore-if-logged-in', 'label' => 'Ignore Logged In Users?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "SpamZap2 will not check logged in users is this is set to yes."]);

            $form->addField('inputtext', ['name' => 'comment-chars', 'label' => 'Comment Characters', 
                'title' => "Enter the number of comment characters to store in the logs.", 'style' => 'width: 10em'])
                ->addValidator(new NumericBetweenValidator(['Log lines'], ['high' => 1000, 'low' => 25]));

            $form->addField('inputtext', ['name' => 'log-lines', 'label' => 'Log Lines', 
                'title' => "Enter the number of log lines to display on the main page.", 'style' => 'width: 10em'])
                ->addValidator(new NumericBetweenValidator(['Log lines'], ['high' => 1000, 'low' => 5]));

        $form->addField('divclose', ['name' => 'row3close']);

        // Row four.
        $form->addField('divopen', ['name' => 'row4', 'class' => 'three-columns']);

            $form->addField('radioset', ['name' => 'collect-password', 'label' => 'Collect Passwords?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Collect passwords for login failures?"]);

            $form->addField('inputtext', ['name' => 'secret1', 'label' => 'Secret 1', 
                'title' => "Used to encrypt passwords in the database.", 'style' => 'width: 10em']);

            $form->addField('inputtext', ['name' => 'secret2', 'label' => 'Secret 2', 
                'title' => "Used to encrypt passwords in the database.", 'style' => 'width: 10em']);

        $form->addField('divclose', ['name' => 'row4close']);

        // Row five.
        $form->addField('divopen', ['name' => 'row5', 'class' => 'three-columns']);

            $form->addField('radioset', ['name' => 'block-all', 'label' => 'Block All?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Block all except logged in administrators. Useful in a bad attack."]);

            $form->addField('radioset', ['name' => 'dummy-mode', 'label' => 'Dummy Mode?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Run in dummy mode? This just will perform no actual blocks."]);

            $form->addField('radioset', ['name' => 'debug-mode', 'label' => 'Debug Mode?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Run in debug mode? Writes debugging messages to the tech log."]);

        $form->addField('divclose', ['name' => 'row5close']);

        // Row six.
        $form->addField('divopen', ['name' => 'row6', 'class' => 'three-columns']);

            $form->addField('inputtext', ['name' => 'temp-block-days', 'label' => 'Temp Block Days', 
                'title' => "Days you wany temporary IP blocks to run for.", 'style' => 'width: 10em'])
                ->addValidator(new NumericBetweenValidator(['Temp block days'], ['high' => 1000, 'low' => 1]));

        $form->addField('divclose', ['name' => 'row6close']);

        // End stuff.
        $form->addField('buttonsubmit', ['name' => 'submit', 'value' => 'Submit', 'style' => 'width: 10em']);

        $form->addRawField(\wp_nonce_field('options'));

        $form->setAutofocus('check-comments');

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

        $defs = Arr::fromArray($this->getFormDefaults());
        $form = $this->createOptionsForm()->load($defs);

        $msgs = [];
        $errors = [];

        //var_dump($_REQUEST);
        //throw new \Exception('123');

        if (isset($_POST['form-submitted']) and $_POST['form-submitted'] === 'options') {
            if (!\wp_verify_nonce($_REQUEST['_wpnonce'], 'options')) {
                wp_die('Security error');
            }
            if ($form->validate($_POST)) {
                $settings = $this->parent->getApp()->get('dbaccess')->getSettings();
                unset($settings['log-count']);
                unset($settings['manage-rules-sel']);
                $sm = $this->parent->getApp()->get('settingsmodel');
                foreach(array_keys($settings) as $k) {
                    $sm->update($k, ['value' => strval($_POST[$k])]);
                }
                $thisUrl = \admin_url('admin.php') . '?page=spamzap2-options';
                echo("<script>location.href = '" . $thisUrl . "'</script>");
            }
        }

        $tplData = [
            'optionsForm'   =>  $form,
            'msgs'          =>  $msgs,
            'errors'        =>  $errors,
        ];

        $template = $this->parent->getApp()->get('template');

        echo $template->render('adminOptions', $tplData);

    }

}
