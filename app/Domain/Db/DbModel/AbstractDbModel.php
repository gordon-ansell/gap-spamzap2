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
namespace App\Domain\Db\DbModel;

use GreenFedora\Db\Model\AbstractCrudModel;
use GreenFedora\Db\Model\CrudModelInterface;
use App\Domain\Db\DbAccess;
use GreenFedora\Db\DbInterface;

/**
 * Base DB model class.
 */
abstract class AbstractDbModel extends AbstractCrudModel implements CrudModelInterface
{
    /**
     * Constructor.
     * 
     * @param   DbAccess    $dbAccess   Database accessor.
     * @return  void
     * #[Inject (dbAccess: dbaccess)]
     */
    public function __construct(DbAccess $dbAccess = null)
    {
        $this->dbAccess = $dbAccess; 
    }

    /**
     * Get the database.
     * 
     * @return  DbInterface
     */
    public function getDb(): DbInterface
    {
        return $this->dbAccess->getDb();
    }

    /**
     * Get the datetime.
     * 
     * @param
     * @return  string
     */
    public function getDt(): string
    {
        $dt = new \DateTime("now", new \DateTimeZone('UTC'));
        return $dt->format(\DateTimeInterface::ATOM);        
    }

    /**
     * Convert a date/time.
     * 
     * @param   string      $dt         Date/time to convert.
     * @return  string                  Converted date/time.
     */
    public function convDt(string $dt): string
    {
        $conv = new \DateTime($dt, new \DateTimeZone('UTC'));
        $tz = \get_option('timezone_string');
        $conv->setTimezone(new \DateTimeZone($tz));
        return $conv->format("Y-m-d H:i");        
    }
}
