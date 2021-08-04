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
use GreenFedora\Html\TableMaker\Pager;
use GreenFedora\Html\TableMaker\PagerInterface;

/**
 * Auth log page processor.
 */
class AuthLogPageProcessor extends AbstractPageProcessor implements PageProcessorInterface
{
    /**
     * Create the log table.
     * 
     * @return  TableMakerInterface 
     */
    protected function createLogTable(): TableMakerInterface
    {
        $table = new TableMaker(['id' =>'authlog-table', 'name' => 'authlog-table']);
        $table->thead()
            ->addColumn('Date/Time', 'dt', 'size-15 left')
            ->addColumn('IP', null, 'size-12 left')
            ->addColumn('User', 'username', 'size-10 left')
            ->addColumn('User Exists', 'userexists', 'size-10 left');

        return $table;
    }

    /**
     * Create the log sub table.
     * 
     * @return  TableMakerInterface
     */
    protected function createLogSubTable(): TableMakerInterface
    {
        $table = new TableMaker([], 'vertical');
        $table->thead()
            ->addColumn('Username', 'username2')
            ->addColumn('Password', 'pwd')
            ->addColumn('IP', 'ip2')
            ->addColumn('Seen (IP/24)', 'seen')
            ->addColumn('CIDR(s)', 'cidrs')
            ->addColumn('Name')
            ->addColumn('Network Name', 'netname')
            ->addColumn('Country')
            ->addColumn('Address')
            ->addColumn('Domain')
            ->addColumn('Network Status');
        
        return $table;

    }

    /**
     * Load records.
     * 
     * @param   TableMakerInterface     $table      Table.
     * @param   array                   $records    Records to load.
     * @param   PagerInterface          $pager      Pager.
     * @return
     */
    protected function loadRecords(TableMakerInterface $table, array $records, PagerInterface $pager)
    {
        $count = 1;
        foreach ($records as $record) {
            $table->tbody()->addRow($count, $record);
            $subtable = $this->createLogSubTable();
            $subtable->tbody()->addRow(1, $record);
            $table->tbody()->getRow($count)->addSubRow(1, $subtable);
            $count++;
        }

        $table->tfoot()->setSpansAll()->addRow(1, [$pager]);
    }

    /**
     * Process.
     * 
     * @return
     */
    public function process()
    {
        $mt = microtime(true);

        // Get the model and create the table.
        $alm = $this->parent->getApp()->get('authlogmodel');
        $tm = $this->createLogTable();

        // Flag new records.
        $logCount = $alm->getSimpleCount();
        $settings = $this->parent->getApp()->get('dbaccess')->getSettings(true);
        $logOld = intval($settings['authlog-count']);
        $logNew = $logCount - $logOld;

        // Create the pager.
        $pageLink = $logUrl = \admin_url('admin.php') . '?page=spamzap2';
        $totalRecs = $alm->recordCount();
        $page = 1;
        if (isset($_GET['pg'])) {
            $page = intval($_GET['pg']);
        }
        $pager = new Pager($page, $totalRecs, intval($settings['log-lines']), $pageLink);

        // Process the database records for display.
        $records = $alm->getRecords($pager->startRec());

        // If we have some records.
        if (count($records) > 0) {
            $processed = $alm->processRecordsForDisplay($records, $logNew, 
                $this->parent->getApp()->getConfig('plugin.slug'));

            // Load the records in to the table.
            $this->loadRecords($tm, $processed, $pager);
        } else {
            $tm = null;
        }

        // Update the count for flagging new records.
        $sm = $this->parent->getApp()->get('settingsmodel');
        $sm->update('authlog-count', ['value' => strval($logCount)]);

        // Clear down the message fields.
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

        $elapsed = microtime(true) - $mt;

        // Display the output.
        $tplData = [
            'msgs'          =>  $msgs,
            'errors'        =>  $errors,
            'elapsed'       =>  $elapsed,
            'lognew'        =>  $logNew,
            'tm'            =>  $tm,
        ];

        $template = $this->parent->getApp()->get('template');

        echo $template->render('adminAuthLogs', $tplData);

    }

}
