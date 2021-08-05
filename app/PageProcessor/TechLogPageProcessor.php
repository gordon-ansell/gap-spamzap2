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
 * Tech log page processor.
 */
class TechLogPageProcessor extends AbstractPageProcessor implements PageProcessorInterface
{
    /**
     * Create the log table.
     * 
     * @return  TableMakerInterface 
     */
    protected function createLogTable(): TableMakerInterface
    {
        $table = new TableMaker(['id' =>'techlog-table', 'name' => 'techlog-table']);
        $table->thead()
            ->addColumn('Date/Time', 'dt', 'size-15 left')
            ->addColumn('IP', null, 'size-12 left')
            ->addColumn('Type', null, 'size-10 left')
            ->addColumn('Message', null, 'size-50 left');

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
            if ('1' == $record['type']) {
                $record['type'] = 'Error';
            } else if ('2' == $record['type']) {
                $record['type'] = 'Debug';
            } else if ('3' == $record['type']) {
                $record['type'] = 'Info';
            }
            $table->tbody()->addRow($count, $record, strtolower($record['type']));
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
        $alm = $this->parent->getApp()->get('techlogmodel');
        $tm = $this->createLogTable();

        // Get the settings.
        $settings = $this->parent->getApp()->get('dbaccess')->getSettings(true);

        // Create the pager.
        $pageLink = $logUrl = \admin_url('admin.php') . '?page=spamzap2-tech-logs';
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
            // Load the records in to the table.
            $this->loadRecords($tm, $records, $pager);
        } else {
            $tm = null;
        }

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
            'tm'            =>  $tm,
        ];

        $template = $this->parent->getApp()->get('template');

        echo $template->render('adminTechLogs', $tplData);

    }

}
