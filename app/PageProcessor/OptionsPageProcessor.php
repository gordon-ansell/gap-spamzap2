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

            $form->addField('radioset', ['name' => 'ignore-if-logged-in', 'label' => 'Ignore Logged In Users?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "SpamXap2 will not check logged in users is this is set to yes."]);

            $form->addField('inputtext', ['name' => 'comment-chars', 'label' => 'Comment Characters', 
                'title' => "Enter the number of comment characters to store in the logs.", 'style' => 'width: 10em'])
                ->addValidator(new NumericBetweenValidator(['Log lines'], ['high' => 1000, 'low' => 25]));

            $form->addField('inputtext', ['name' => 'log-lines', 'label' => 'Log Lines', 
                'title' => "Enter the number of log lines to display on the main page.", 'style' => 'width: 10em'])
                ->addValidator(new NumericBetweenValidator(['Log lines'], ['high' => 1000, 'low' => 25]));

        $form->addField('divclose', ['name' => 'row2close']);

        // Row three.
        $form->addField('divopen', ['name' => 'row3', 'class' => 'three-columns']);

            $form->addField('radioset', ['name' => 'debug-mode', 'label' => 'Debug Mode?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Run in debug mode?"]);

            $form->addField('radioset', ['name' => 'dummy-mode', 'label' => 'Dummy Mode?', 'class' => 'radio', 
                'options' => ['1' => 'Yes', '0' => 'No'], 'style' => 'width: 10em',
                'title' => "Run in dummy mode? This just will perform no actial blocks."]);

        $form->addField('divclose', ['name' => 'row3close']);


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