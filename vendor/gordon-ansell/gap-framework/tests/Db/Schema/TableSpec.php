<?php 
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Tests\Db\Schema;

use GreenFedora\Db\Schema\Schema;
use GreenFedora\Tests\Db\Schema\Table1;
use GreenFedora\Tests\Db\Schema\Table2;

/**
 * Schema.
 */
class TableSpec extends Schema
{
    /**
     * Table spec.
     * @var array
     */
    protected $tableSpec = array(
        Table1::class => 'table1',
        Table2::class => 'table2',
    );

}
