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
use GreenFedora\Html\TableMaker\TableMaker;
use GreenFedora\Html\TableMaker\TableMakerInterface;
use GreenFedora\Stdlib\Path;
use GreenFedora\Html\Html;


/**
 * Auth count page processor.
 */
class AuthCountPageProcessor extends AbstractPageProcessor implements PageProcessorInterface
{
    /**
     * Create a type 1 table.
     * 
     * @param   array   $recs       Records to load.
     * @param   string  $idfield    ID field name.
     * 
     * @return TableMakerInterface
     */
    protected function createTable(array $recs, string $idfield = 'authcount_id')
    {
        $table = new TableMaker(['id' =>'authcount-table', 'name' => 'authcount-table', 'class' => 'stripe']);
        $table->thead()
            ->addColumn('ID', $idfield, 'size-8 left')
            ->addColumn('Latest', 'latest', 'size-15 left')
            ->addColumn('IP', null, 'size-15 left')
            ->addColumn('Count', 'ipcount', 'size-15 right');
 
        // IP bans.
        $slug = $this->parent->getApp()->getConfig('plugin.slug');
        $slugUrl = Path::join(\plugin_dir_url($slug), $slug);
        $iconUrl = Path::join($slugUrl, 'assets', 'icons');
        $banUrl = \admin_url('admin.php') . '?page=spamzap2-add-rule&back=authcount';
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

        $count = 1;
        
        foreach ($recs as $rec) {
            $ipBanLink->setParam('href', $banUrl . '&ip=' . $rec['ip']);
            $rec['ip'] = $ipBanLink->render($ipBanIconR) . $rec['ip']; 
            $rec['latest'] = $this->convDt($rec['latest']);
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

        // Set the message fields from the session.
        if (isset($_SESSION['sz2-e'])) {
            $errors = [$_SESSION['sz2-e']];
            unset($_SESSION['sz2-e']);
        }
        if (isset($_SESSION['sz2-m'])) {
            $msgs = [$_SESSION['sz2-m']];
            unset($_SESSION['sz2-m']);
        }

        $model = $this->parent->getApp()->get('authcountmodel');
        $recs = $model->listAll('latest', 'desc');
        $table = null;
        if (count($recs) > 0) {
            $table = $this->createTable($recs);
        }

        $tplData = [
            'msgs'              =>  $msgs,
            'errors'            =>  $errors,
            'table'             =>  $table,
        ];

        $template = $this->parent->getApp()->get('template');

        echo $template->render('adminAuthCount', $tplData);

    }


}
