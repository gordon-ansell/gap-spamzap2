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
namespace App\Domain\Db\Schema;

use GreenFedora\Db\Schema\Schema;
use App\Domain\Db\Schema\IPBlockTable;
use App\Domain\Db\Schema\IPAllowTable;

/**
 * App schema.
 */
class AppSchema extends Schema
{
    /**
     * Table spec.
     * @var array
     */
    protected $tableSpec = array(
        IPBlockTable::class => 'ipblock',
        IPTempBlockTable::class => 'iptempblock',
        IPAllowTable::class => 'ipallow',
        AuthErrorTable::class => 'autherror',
        IPLookupTable::class => 'iplookup',
        DomainBlockTable::class => 'domainblock',
        EmailBlockTable::class => 'emailblock',
        StringBlockTable::class => 'stringblock',
        LogTable::class => 'log',
        TechLogTable::class => 'techlog',
        SettingsTable::class => 'settings',
    );
}
