<?php
/**
 * GreenFedora PHP Library.
 *
 * @copyright   Gordon Ansell, 2017.
 */
 
declare(strict_types=1);
namespace GreenFedora\Db\Driver\Stmt\Exception;

use GreenFedora\Db\Driver\Stmt\Exception\ExceptionInterface;

class DbStmtException extends \RuntimeException implements ExceptionInterface
{
}
