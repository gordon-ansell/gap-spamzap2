<?php
/**
 * This file is part of the GF package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace Gf;

use GreenFedora\Application\ApplicationInterface;
use GreenFedora\Console\ConsoleApplication;
use GreenFedora\Stdlib\Path;

/**
 * The main application entry point.
 */
class Gf extends ConsoleApplication
{
    /**
     * Application version.
     * @param string
     */
    protected static $version = '1.0.0-dev';

    /**
     * Greenfedora startup directory.
     * @var string
     */
    protected $gfstart = null;

    /**
     * Whete to generate.
     * @var string
     */
    protected $path = null;

    /**
     * Operation mode.
     * @var string
     */
    protected $opMode = null;

    /**
     * Constructor.
     * 
     * @param   string  $basePath   Base path to the application.
     * @param   string  $appPath    Application path.
     * @return  void
     */
    public function __construct(string $basePath, string $appPath)
    {
        parent::__construct($basePath, $appPath);
    }

    /**
     * Get the mode.
     * 
     * @return string
     */
    public function getOpMode(): string
    {
        return $this->opMode;
    }

    /**
     * Get the path.
     * 
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Initialisation.
     * 
     * @return  ApplicationInterface
     */
    public function init(): ApplicationInterface
    {
        /*
        $locations = $this->getConfig('locations');

        if (file_exists(Path::join($this->gfstart, $locations->app))) {
            $this->path = Path::join($this->gfstart, $locations->app);
            $this->opMode = 'app';
            logger()->debug("GreenFedora helper running in app mode.");
        } else if (file_exists(Path::join($this->gfstart, $locations->fw))) {
            $this->path = Path::join($this->gfstart, $locations->fw);
            $this->opMode = 'fw';
            logger()->debug("GreenFedora helper running in framework mode.");
        } else {
            logger()->error("GreenFedora helper cannot determine execution mode (app or fw?). This is terrible news.");
        }
        */

        return $this;
    }
}
