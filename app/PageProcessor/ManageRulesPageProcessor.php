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
use GreenFedora\Html\TableMaker\TableMaker;
use GreenFedora\Html\TableMaker\TableMakerInterface;
use GreenFedora\Html\Html;


/**
 * Manage rules page processor.
 */
class ManageRulesPageProcessor extends AbstractPageProcessor implements PageProcessorInterface
{
    /**
     * Get the form defaults.
     * 
     * @return array
     */
    protected function getFormDefaults(): array
    {
        $settings = $this->parent->getApp()->get('dbaccess')->getSettings(true);
        return array(
            'manage-rules-sel' => $settings['manage-rules-sel'], 
        );        
    }

    /**
     * Create the add rule form.
     * 
     * @return  FormInterface 
     */
    protected function createManageRulesSelForm(): FormInterface
    {
        $form = new Form('manage-rules', '');
        $form->setAutoWrap('fieldset');
        $form->setCsrf(false);

        $form->addField('errors', ['name' => 'sz-errors', 'class' => 'formerror']);

        $form->addField('select', [
            'name' => 'manage-rules-sel',
            'options'   =>  [
                '1' => "IP Blocks",
                '2' => "Domain Blocks",
                '3' => "Email Blocks",
                '4' => "String Blocks",
                '5' => "IP Allows",
            ],
            'label' => 'Rule Set',
            'class' => 'nowrap',
            'onchange' => "this.form.submit()",
        ]);

        //$form->addField('buttonsubmit', ['name' => 'submit', 'value' => 'Submit', 'style' => 'width: 10em']);

        $form->addRawField(\wp_nonce_field('manage-rules'));

        $form->setAutofocus('manage-rules-sel');

        return $form;
         
    }

    /**
     * Create a type 1 table.
     * 
     * @param   array   $recs       Records to load.
     * @param   string  $idfield    ID field name.
     * 
     * @return TableMakerInterface
     */
    protected function createTable1(array $recs, string $idfield = 'ipblock_id')
    {
        $table = new TableMaker(['id' =>'ip-table', 'name' => 'ip-table', 'class' => 'stripe']);
        $table->thead()
            ->addColumn('ID', $idfield, 'size-8 left')
            ->addColumn('Date/Time', 'dt', 'size-15 left')
            ->addColumn('IP', null, 'size-15 left');

        $slug = $this->parent->getApp()->getConfig('plugin.slug');
        $slugUrl = Path::join(\plugin_dir_url($slug), $slug);
        $iconUrl = Path::join($slugUrl, 'assets', 'icons');
        
        $delIcon = new Html('img', [
            'title' => 'Delete this record.', 
            'alt' => "Generic delete icon.", 
            'class' => 'icon del',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $delIconR = $delIcon->render();

        $delUrl = \admin_url('admin.php') . '?page=spamzap2-manage-rules&field=' . $idfield;
        $delLink = new Html('a', [
            'href' => $delUrl,
            'title' => 'Delete this entry.',
        ]);

        $count = 1;
        
        foreach ($recs as $rec) {
            $rec['dt'] = $this->convDt($rec['dt']);
            $delLink->setParam('href', $delUrl . '&id=' . $rec[$idfield]);
            $rec[$idfield] = $delLink->render($delIconR) . $rec[$idfield];
            $table->tbody()->addRow($count, $rec);
            $count++;
        }

        return $table;
        
    }

    /**
     * Create a type 2 table.
     * 
     * @param   array   $recs       Records to load.
     * @param   string  $idfield    ID field name.
     * 
     * @return TableMakerInterface
     */
    protected function createTable2(array $recs, string $idfield = 'domainblock_id')
    {
        $table = new TableMaker(['id' =>'domem-table', 'name' => 'domem-table', 'class' => 'stripe']);
        $table->thead()
            ->addColumn('ID', $idfield, 'size-8 left')
            ->addColumn('Date/Time', 'dt', 'size-15 left')
            ->addColumn('Value', 'item', 'size-50 left')
            ->addColumn('Is Regex?', 'isregex', 'size-8 left');

        $slug = $this->parent->getApp()->getConfig('plugin.slug');
        $slugUrl = Path::join(\plugin_dir_url($slug), $slug);
        $iconUrl = Path::join($slugUrl, 'assets', 'icons');
        
        $delIcon = new Html('img', [
            'title' => 'Delete this record.', 
            'alt' => "Generic delete icon.", 
            'class' => 'icon del',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $delIconR = $delIcon->render();

        $delUrl = \admin_url('admin.php') . '?page=spamzap2-manage-rules&field=' . $idfield;
        $delLink = new Html('a', [
            'href' => $delUrl,
            'title' => 'Delete this entry.',
        ]);

        $count = 1;
        
        foreach ($recs as $rec) {
            $rec['dt'] = $this->convDt($rec['dt']);
            $rec['isregex'] = ('1' == $rec['isregex']) ? 'Yes' : 'No';
            $rec['item'] = stripslashes($rec['item']);
            $delLink->setParam('href', $delUrl . '&id=' . $rec[$idfield]);
            $rec[$idfield] = $delLink->render($delIconR) . $rec[$idfield];
            $table->tbody()->addRow($count, $rec);
            $count++;
        }

        return $table;
        
    }

    /**
     * Create a type 3 table.
     * 
     * @param   array   $recs       Records to load.
     * @param   string  $idfield    ID field name.
     * 
     * @return TableMakerInterface
     */
    protected function createTable3(array $recs, string $idfield = 'stringblock_id')
    {
        $table = new TableMaker(['id' =>'string-table', 'name' => 'string-table', 'class' => 'stripe']);
        $table->thead()
            ->addColumn('ID', $idfield, 'size-8 left')
            ->addColumn('Date/Time', 'dt', 'size-15 left')
            ->addColumn('Value', 'item', 'size-40 left')
            ->addColumn('Usernames?', 'username', 'size-8 left')
            ->addColumn('Comments?', 'comment', 'size-8 left')
            ->addColumn('Is Regex?', 'isregex', 'size-8 left');

        $slug = $this->parent->getApp()->getConfig('plugin.slug');
        $slugUrl = Path::join(\plugin_dir_url($slug), $slug);
        $iconUrl = Path::join($slugUrl, 'assets', 'icons');
        
        $delIcon = new Html('img', [
            'title' => 'Delete this record.', 
            'alt' => "Generic delete icon.", 
            'class' => 'icon del',
            'src' => Path::join($iconUrl, 'block.png'),
        ]);
        $delIconR = $delIcon->render();

        $delUrl = \admin_url('admin.php') . '?page=spamzap2-manage-rules&field=' . $idfield;
        $delLink = new Html('a', [
            'href' => $delUrl,
            'title' => 'Delete this entry.',
        ]);

        $count = 1;
        
        foreach ($recs as $rec) {
            $rec['dt'] = $this->convDt($rec['dt']);
            $rec['isregex'] = ('1' == $rec['isregex']) ? 'Yes' : 'No';
            $rec['username'] = ('1' == $rec['username']) ? 'Yes' : 'No';
            $rec['comment'] = ('1' == $rec['comment']) ? 'Yes' : 'No';
            $rec['item'] = stripslashes($rec['item']);
            $delLink->setParam('href', $delUrl . '&id=' . $rec[$idfield]);
            $rec[$idfield] = $delLink->render($delIconR) . $rec[$idfield];
            $table->tbody()->addRow($count, $rec);
            $count++;
        }

        return $table;
        
    }

    /**
     * Process.
     * 
     * @return
     */
    public function process()
    {
        $msgs = [];
        $errors = [];

        if (isset($_SESSION['sz2-e'])) {
            $errors = [$_SESSION['sz2-e']];
            unset($_SESSION['sz2-e']);
        }
        if (isset($_SESSION['sz2-m'])) {
            $msgs = [$_SESSION['sz2-m']];
            unset($_SESSION['sz2-m']);
        }

        if (isset($_GET['field']) and isset($_GET['id'])) {
            $mrUrl = \admin_url('admin.php') . '?page=spamzap2-manage-rules';

            $model = null;
            switch ($_GET['field']) {
                case 'ipblock_id':
                    $model = $this->parent->getApp()->get('ipblockmodel');
                    break;

                case 'ipallow_id':
                    $model = $this->parent->getApp()->get('ipallowmodel');
                    break;

                case 'domainblock_id':
                    $model = $this->parent->getApp()->get('domainblockmodel');
                    break;

                case 'emailblock_id':
                    $model = $this->parent->getApp()->get('emailblockmodel');
                    break;

                case 'stringblock_id':
                    $model = $this->parent->getApp()->get('stringblockmodel');
                    break;

            }

            if (!is_null($model)) {
                $model->delete(intval($_GET['id']));
                $_SESSION['sz2-m'] = "Record deleted.";
                echo("<script>location.href = '" . $mrUrl . "'</script>");
                return;
            }
        }

        $dt = $this->getDt();

        $defs = Arr::fromArray($this->getFormDefaults());
        $form = $this->createManageRulesSelForm()->load($defs);

        if (isset($_POST['form-submitted']) and $_POST['form-submitted'] === 'manage-rules') {
            if (!\wp_verify_nonce($_REQUEST['_wpnonce'], 'manage-rules')) {
                wp_die('Security error');
            }
            if ($form->validate($_POST)) {

                $sm = $this->parent->getApp()->get('settingsmodel');
                $sm->update('manage-rules-sel', ['value' => strval($_POST['manage-rules-sel'])]);
                $defs = Arr::fromArray($this->getFormDefaults());
                $form = $this->createManageRulesSelForm()->load($defs);
        
           }
        }

        $settings = $this->parent->getApp()->get('dbaccess')->getSettings(true);
        $ruleset = intval($settings['manage-rules-sel']);
        if (empty($ruleset)) {
            $ruleset = 1;
        }

        $model = null;
        switch ($ruleset) {
            case 1:
                $model = $this->parent->getApp()->get('ipblockmodel');
                break;
            case 2:
                $model = $this->parent->getApp()->get('domainblockmodel');
                break;
            case 3:
                $model = $this->parent->getApp()->get('emailblockmodel');
                break;
            case 4:
                $model = $this->parent->getApp()->get('stringblockmodel');
                break;
            case 5:
                $model = $this->parent->getApp()->get('ipallowmodel');
                break;
        } 

        $table = null;
        if (1 == $ruleset or 5 == $ruleset) {
            $recs = $model->listAll('range_start_long');
            if (count($recs) > 0) {
                $f = (5 == $ruleset) ? 'ipallow_id' : 'ipblock_id';
                $table = $this->createTable1($recs, $f);
            }
        } else if (4 == $ruleset) {
            $recs = $model->listAll('item');
            if (count($recs) > 0) {
                $table = $this->createTable3($recs);
            }
        } else {
            $recs = $model->listAll('item');
            if (count($recs) > 0) {
                $f = (3 == $ruleset) ? 'emailblock_id' : 'domainblock_id';
                $table = $this->createTable2($recs, $f);
            }
        }

        $tplData = [
            'manageRulesForm'   =>  $form,
            'msgs'              =>  $msgs,
            'errors'            =>  $errors,
            'table'             =>  $table,
        ];

        $template = $this->parent->getApp()->get('template');

        echo $template->render('adminManageRules', $tplData);

    }


}
