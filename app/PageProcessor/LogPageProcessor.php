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
     * Create the table to contain all the data.
     * 
     * @return
     */
    /*
    protected function createTable(): TableInterface
    {
        $table = new Table('log-table', 'flextable stripe spamzap2 log');

        $table->addColumn('dt', 'Date/Time', 'size-15 left')
            ->addColumn('type', 'T', 'size-2 left')
            ->addColumn('ip', 'IP', 'size-12 left')
            ->addColumn('username', 'User', 'size-10 left')
            ->addColumn('email', 'Email', 'size-21 left breakword hide2')
            ->addColumn('matchtype', 'Match Type', 'size-12 left')
            ->addColumn('matchval', 'Match Val', 'size-28 left');
            //->addColumn('status', 'Status', 'size-8 left hide2');

        $table->addSubRow('sr', 'subrow');
        $table->getSubRow('sr')
            ->addColumn('username2', 'Username')
            ->addColumn('email2', 'Email')
            ->addColumn('emaildomain', 'Email Domain')
            ->addColumn('rawemaildomain', 'Raw Email Domain')
            ->addColumn('blank1', '')
            ->addColumn('commentauthorurl', 'Author URL')
            ->addColumn('commentauthordom', 'Author Domain')
            ->addColumn('commentposttitle', 'Post Title')
            ->addColumn('commentpostid', 'Post ID')
            ->addColumn('comment', 'Comment')
            ->addColumn('commentdomains', 'Comment Domains')
            ->addColumn('blank2', '')
            ->addColumn('ip2', 'IP')
            ->addColumn('seen', 'Seen (IP/24)')
            ->addColumn('cidrs', 'CIDR(s)')
            ->addColumn('name', 'Name')
            ->addColumn('netname', 'Network Name')
            ->addColumn('country', 'Country')
            ->addColumn('address', 'Address')
            ->addColumn('domain', 'Domain')
            ->addColumn('networkstatus', 'Network Status');

        return $table;
    }
    */

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

    /*
    protected function doTest()
    {
        $inner = new TableMaker(['class' => 'subtable'], 'vertical');
        $inner->thead()->addColumn('one')->addColumn('two');
        $inner->tbody()->addRow(1, ['one' => 'number 1', 'two' => 'number 2']);


        $table = new TableMaker(['id' =>'test-table']);

        $table->thead()->addColumn('Col1', null, ['class' => 'size-50'])
            ->addColumn('Blah', null, ['class' => 'size-25 hide1'])
            ->addColumn('Here', null, ['class' => 'size-25']);


        $table->tbody()->addRow(1, ['col1' => 'Hello', 'blah' => 'How are', 'here' => 'you']);
        $table->tbody()->addRow(2, ['col1' => 'Black', 'blah' => 'is the', 'here' => 'color']);
        $table->tbody()->getRow(1)->addSubRow(1, $inner);


        return $table;
    }
    */

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
        //$table = $this->createTable();
        $tm = $this->createLogTable();

        $logCount = $lm->getSimpleCount();
        $settings = $this->parent->getApp()->get('dbaccess')->getSettings(true);
        $logOld = intval($settings['log-count']);
        $logNew = $logCount - $logOld;

        $records = $lm->getRecords();
        $processed = $lm->processRecordsForDisplay($records, $logNew, 
            $this->parent->getApp()->getConfig('plugin.slug'));
        //$table->setData($processed);

        // TESTING ====================================================
        $this->loadRecords($tm, $processed);


        // ============================================================

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

        //$testTable = $this->doTest();

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
