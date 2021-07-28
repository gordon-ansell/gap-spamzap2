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
use App\Domain\TypeCodes;
use GreenFedora\Html\Html;
use GreenFedora\Html\TableMaker\TableMaker;
use GreenFedora\Html\TableMaker\TableMakerInterface;
use GreenFedora\Stdlib\Path;
use GreenFedora\Table\TableInterface;
use GreenFedora\Table\Table;

/**
 * Log page processor.
 */
class LogPageProcessor extends AbstractPageProcessor implements PageProcessorInterface
{
    /**
     * Create the log table.
     * 
     * @return  TableMakerInterface 
     */
    protected function createLogTable(): TableMakerInterface
    {
        $table = new TableMaker(['id' =>'log-table', 'name' => 'log-table']);
        $table->thead()
            ->addColumn('Date/Time', 'dt', 'size-15 left')
            ->addColumn('T', 'type', 'size-2 left')
            ->addColumn('IP', null, 'size-12 left')
            ->addColumn('User', 'username', 'size-10 left')
            ->addColumn('Email', null, 'size-21 left breakword hide2')
            ->addColumn('Match Type', null, 'size-12 left')
            ->addColumn('Match Val', null, 'size-28 left');

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
            ->addColumn('Email', 'email2')
            ->addColumn('Email Domain')
            ->addColumn('Raw Email Domain')
            //->addColumn('blank1', '')
            ->addColumn('Author URL', 'commentauthorurl')
            ->addColumn('Author Domain', 'commentauthordom')
            ->addColumn('Post Title', 'commentposttitle')
            ->addColumn('Post ID', 'commentpostid')
            ->addColumn('Comment')
            ->addColumn('Comment Domains')
            //->addColumn('blank2', '')
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
     * @return
     */
    protected function loadRecords(TableMakerInterface $table, array $records)
    {
        $count = 1;
        foreach ($records as $record) {
            $class = 'status-' . strtolower($record['status']);
            if ("1" == $record['isdummy']) {
                $class .= ' dummy';
            }
            $table->tbody()->addRow($count, $record, $class);
            if ('Inf' != $record['rawtype']) {
                $subtable = $this->createLogSubTable();
                $subtable->tbody()->addRow(1, $record);
                $table->tbody()->getRow($count)->addSubRow(1, $subtable);
            }
            $count++;
        }
    }

    /**
     * Process.
     * 
     * @return
     */
    public function process()
    {
        $mt = microtime(true);
        $lm = $this->parent->getApp()->get('logmodel');
        $tm = $this->createLogTable();

        $logCount = $lm->getSimpleCount();
        $settings = $this->parent->getApp()->get('dbaccess')->getSettings(true);
        $logOld = intval($settings['log-count']);
        $logNew = $logCount - $logOld;

        $records = $lm->getRecords();
        $processed = $lm->processRecordsForDisplay($records, $logNew, 
            $this->parent->getApp()->getConfig('plugin.slug'));

        $this->loadRecords($tm, $processed);

        $sm = $this->parent->getApp()->get('settingsmodel');
        $sm->update('log-count', ['value' => strval($logCount)]);

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

        $elapsed = microtime(true) - $mt;

        $tplData = [
            'msgs'          =>  $msgs,
            'errors'        =>  $errors,
            'elapsed'       =>  $elapsed,
            'lognew'        =>  $logNew,
            'tm'            =>  $tm,
        ];

        $template = $this->parent->getApp()->get('template');

        echo $template->render('adminLogs', $tplData);

    }

}
