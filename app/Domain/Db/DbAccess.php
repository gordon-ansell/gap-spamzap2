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
namespace App\Domain\Db;

use GreenFedora\Db\DbInterface;
use GreenFedora\Db\Db;
use GreenFedora\Db\Driver\PdoDriver;
use GreenFedora\Db\Platform\Sqlite;
use GreenFedora\Stdlib\Arr\ArrInterface;

use App\Domain\Db\Schema\AppSchema;

/**
 * Database activities.
 */
class DbAccess
{
    /**
     * Version.
     * @var string
     */
    protected $version = null;

    /**
     * Base path.
     * @var string
     */
    protected $basePath = null;

    /**
     * Configs.
     * @var ArrInterface
     */
    protected $cfg = null;

    /**
     * Database.
     * @var DbInterface
     */
    protected $db = null;

    /**
     * Settings.
     * @var array
     */
    protected $settings = [];
    
    /**
     * Constructor.
     * 
     * @param   string          $version    Software version number.
     * @param   string          $basePath   Base path to the application.
     * @param   ArrInterface    $cfg        DB config.
     * @return  void
     * #[Inject (version: version)]
     * #[Inject (basePath: basePath)]
     * #[Inject (cfg: cfg|db)]
     */
    public function __construct(?string $version = null, ?string $basePath = null, ?ArrInterface $cfg = null)
    {
        $this->version = $version;  
        $this->basePath = $basePath;
        $this->cfg = $cfg;

        $this->initialiseDb();
    }

    /**
     * Initialise the database.
     * 
     * @return void
     */
    public function initialiseDb()
    {
        // Set up the database.
        $this->db = new Db(new PdoDriver($this->cfg), new Sqlite($this->cfg), $this->cfg);

        // Construct the schema and auto refrsh/create/upgrade it.
        $schema = new AppSchema($this->db, $this->version);
        $schema->auto();

        // Check messages.
        $msgs = $schema->getMsgs();
        $logger = app()->get('logger');
        foreach ($msgs as $msg) {
            $logger->debug($msg);
        }

        // Load the settings.
        $this->loadSettings();

    }

    /**
     * Load the settings.
     * 
     * @return void
     */
    public function loadSettings()
    {
        // Fetch the settings.
        $this->settings = [];
        $result = $this->db->select('settings')->fetchArray();
        foreach ($result as $item) {
            $this->settings[$item['settings_id']] = $item['value'];
        }
    }

    /**
     * Get the settings.
     * 
     * @param   bool    $reload     Reload?
     * @return  array
     */
    public function getSettings(bool $reload = false): array
    {
        if ($reload) {
            $this->loadSettings();
        }
        return $this->settings;
    }

    /**
     * Get the DB.
     * 
     * @return  DbInterface
     */
    public function getDb(): DbInterface
    {
        return $this->db;
    }

}
