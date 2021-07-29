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
namespace GreenFedora\Db\Driver;

use GreenFedora\Db\Driver\Stmt\StmtInterface;
use GreenFedora\Logger\LoggerAwareInterface;
use GreenFedora\Logger\LoggerAwareTrait;
use GreenFedora\Logger\LoggerInterface;
use GreenFedora\Stdlib\Arr\ArrInterface;

/**
 * Abstract database driver.
 */
abstract class AbstractDriver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Configs.
     * @var ArrInterface
     */
    protected $config = null;

    /**
     * Constructor.
     *
     * @param   ArrInterface         $config     Configs.
     * @param   LoggerInterface      $logger     Logger.
     * @return  void
     * #[Inject (logger: logger)]
     */
    public function __construct(ArrInterface $config, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger;

        if (true === $this->config->auto) {
            $this->connect();
        }
    }

    /**
     * Connect to the database.
     *
     * @return  void
     * @throws  DbDriverException
     */
    abstract public function connect();

    /**
     * Prepare a statement.
     *
     * @param   string      $sql        SQL to prepare.
     * @param   array       $options    Driver options.
     * @return  StmtInterface
     * @throws  DbStmtException
     */
    abstract public function prepare(string $sql, array $options = array()) : ?StmtInterface;
}
