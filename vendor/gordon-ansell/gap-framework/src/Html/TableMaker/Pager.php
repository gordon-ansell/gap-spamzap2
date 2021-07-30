<?php

/**
 * This file is part of the GordyAnsell GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Html\TableMaker;

use GreenFedora\Html\Html;
use GreenFedora\Html\TableMaker\PagerInterface;

/**
 * Table pager.
 *
 * @author Gordon Ansell <contact@gordonansell.com>
 */

class Pager extends Html implements PagerInterface
{
    /**
     * Page number.
     * @var int
     */
    protected $pageNo = 1;

    /**
     * Total records.
     * @var int
     */
    protected $totalRecs = 0;

    /**
     * Per page.
     * @var int
     */
    protected $perPage = 0;

    /**
     * Number of pages.
     * @var int
     */
    protected $numberOfPages = 1;

    /**
     * Start record.
     * @var int
     */
    protected $startRec = 0;

    /**
     * Page link.
     * @var string
     */
    protected $pageLink = null;

    /**
     * Page parameter name.
     * @var string
     */
    protected $pageParam = 'pg';

    /**
     * Constructor.
     * 
     * @param   int             $pageNo     Page number.
     * @param   int             $totalRecs  Total records.
     * @param   int             $perPage    Per page.
     * @param   string          $pageLink   Page link.
     * @param   array           $params     Parameters.
     * @param   string          $pageParam  Page parameter name.
     * @return  PagerInterface    
     */
    public function __construct(int $pageNo, int $totalRecs, int $perPage, string $pageLink, array $params = [],
        $pageParam = 'pg')
    {
        $this->pageNo = $pageNo;
        $this->totalRecs = $totalRecs;
        $this->perPage = $perPage;
        $this->pageLink = $pageLink;
        $this->pageParam = $pageParam;

        $this->numberOfPages = intval(ceil($totalRecs / $perPage));

        $this->startRec = ($pageNo * $perPage) - $perPage;

        parent::__construct('div', $params);    
    }

    /**
     * Get the start record.
     * 
     * @return  int
     */
    public function startRec(): int
    {
        return $this->startRec;
    }

	/**
	 * Render the foot.
	 *
	 * @param 	string|null	$data 	        Data.
     * @param   array       $extraParams    Extra params for this render.
	 * @return 	string
	 */
	public function render(?string $data = null, ?array $extraParams = null) : string
    {
        $maxRecs = $this->startRec + $this->perPage;
        if ($maxRecs > $this->totalRecs) {
            $maxRecs = $this->totalRecs;
        }

        $ret = '<span class="pageritem recsof">Records ' 
            . ($this->startRec + 1) 
            . ' to ' 
            . $maxRecs 
            . ' of ' 
            . $this->totalRecs
            . '</span>';

        $ret .= '<span class="pageritem pagesof">Page ' 
            . $this->pageNo
            . ' of ' 
            . $this->numberOfPages 
            . '</span>';
        
        if ($this->numberOfPages > 1) {
            $pl = $this->pageLink;

            if (false === strpos($pl, '?')) {
                $pl .= '?' . $this->pageParam . '='; 
            } else {
                $pl .= '&' . $this->pageParam . '=';             
            }

            $pnNav = '';

            if ($this->pageNo > 1) {
                $prev = $pl;
                if ($this->pageNo > 2) {
                    $prev .= ($this->pageNo - 1);
                } else {
                    $prev = $this->pageLink;
                }
                $prevLink = new HTml('a', [
                    'href' => $prev,
                    'title' => 'Go to the previous page.'
                ]);
                $pnNav .= '<span class="prev">' . $prevLink->render("<<< Prev") . '</span>';
            }

            if ($this->pageNo < $this->numberOfPages) {
                $next = $pl . ($this->pageNo + 1);

                $nextLink = new HTml('a', [
                    'href' => $next,
                    'title' => 'Go to the next page.'
                ]);
                if ('' != $pnNav) {
                    $pnNav .= ' | ';
                }
                $pnNav .= '<span class="next">' . $nextLink->render("Next >>>") . '</span>';
            }


            $ret .= '<span class="pageritem pnnav">' . $pnNav . '</span>';
        }

        return parent::render($ret, $extraParams);
    }
}
