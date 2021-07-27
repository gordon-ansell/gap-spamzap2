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
use GreenFedora\Validator\IPAddressValidator;
use GreenFedora\Form\Form;
use GreenFedora\Form\FormInterface;
use GreenFedora\Stdlib\Arr\Arr;
use App\Domain\DoLookup;

/**
 * Lookup IP page processor.
 */
class LookupIpPageProcessor extends AbstractPageProcessor implements PageProcessorInterface
{
    /**
     * Get the form defaults.
     * 
     * @return array
     */
    protected function getFormDefaults(): array
    {
        return array(
            'ip-address' => '',
        );        
    }

    /**
     * Create the lookup form.
     * 
     * @return  FormInterface 
     */
    protected function createLookupForm(): FormInterface
    {

        $form = new Form('lookup-ip', '');
        $form->setAutoWrap('fieldset');
        $form->setCsrf(false);

        $form->addField('errors', ['name' => 'sz-errors', 'class' => 'formerror']);

        $form->addField('inputtext', ['name' => 'ip-lookup', 'label' => 'IP Address', 
            'placeholder' => '192.168.0.0', 'title' => "Enter an IP address to lookup.", 'style' => 'width: 10em'])
            ->addValidator(new IPAddressValidator(['IP block']));

        $form->addField('buttonsubmit', ['name' => 'submit', 'value' => 'Submit', 'style' => 'width: 10em']);
 
        $form->addRawField(\wp_nonce_field('lookup-ip'));

        $form->setAutofocus('ip-lookup');

        return $form;
         
    }

    /**
     * Process.
     * 
     * @return
     */
    public function process()
    {
        $passed = null;
        if (isset($_GET['ip'])) {
            $passed = $_GET['ip'];
            $passed = '191.96.100.84';
        }

        $msgs = [];
        $errors = [];
        $data = [];
        $raw = '';
        $form = null;

        if (is_null($passed)) {
            $defs = Arr::fromArray($this->getFormDefaults());
            $form = $this->createLookupForm()->load($defs);

            if (isset($_POST['form-submitted']) and $_POST['form-submitted'] === 'lookup-ip') {
                if (!\wp_verify_nonce($_REQUEST['_wpnonce'], 'lookup-ip')) {
                    wp_die('Security error');
                }
                if ($form->validate($_POST)) {

                    $ip = $_POST['ip-lookup'];
                    $lu = new DoLookup($ip);
                    $data = $lu->getData();

                    if (isset($data['raw'])) {
                        $raw = $data['raw'];
                        unset($data['raw']);
                    }
                }
            }

        } else {

            $lu = new DoLookup($passed);
            $data = $lu->getData();

        }

        $tplData = [
            'lookupForm'    =>  $form,
            'msgs'          =>  $msgs,
            'errors'        =>  $errors,
            'data'          =>  $data,
            'raw'           =>  $raw,
        ];

        $template = $this->parent->getApp()->get('template');

        echo $template->render('adminLookup', $tplData);

    }

}
