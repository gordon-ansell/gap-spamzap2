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
namespace GreenFedora\Wordpress;

use GreenFedora\Wordpress\WordpressApplicationInterface;
use GreenFedora\Logger\LoggerAwareInterface;
use GreenFedora\Logger\LoggerAwareTrait;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Wordpress\PluginUserInterface;
use GreenFedora\Wordpress\PluginAdminInterface;
use GreenFedora\Wordpress\PluginUpdateInterface;

/**
 * The kernel that drives Wordpress applications.
 */
class WordpressKernel implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Application instance.
     * @var WordpressApplicationInterface
     */
    protected $app = null;

    /**
     * Commands.
     * @var CommandInterface[]
     */
    protected $commands = [];

    /**
     * Constructor.
     * 
     * @param   WordpressApplicationInterface   $app        Application instance.
     * @param   LoggerInterface                 $logger     Logger.
     * @return  void
     */
    public function __construct(WordpressApplicationInterface $app, LoggerInterface $logger = null)
    {
        $this->app = $app;
        if (!is_null($logger)) {
            $this->setLogger($logger);
        }
    }

    /**
     * Initialisation.
     *
     * @return  void
     */
    public function init(): void
    {

    }

    /**
     * Dispatch the input.
     * 
     * @param   PluginUserInterface     $user       User-side stuff.
     * @param   PluginAdminInterface    $admin      Admin-side stuff.
     * @param   PluginUpdateInterface   $update     Update stuff.
     * 
     * @return  void
     */
    public function dispatch(PluginUserInterface $user, ?PluginAdminInterface $admin = null, 
        ?PluginUpdateInterface $update = null): void
    {
        try {
            $this->app->run($user, $admin, $update);

        } catch (\Throwable $ex) {
            $this->alert("Exception encountered dispatching input from the kernel.", $ex);
        }
    }

}
