<?php
/**
 * This file is part of the Gf package.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace Gf\Console\Command;

use GreenFedora\Console\Command\Command;
use GreenFedora\Stdlib\Path;
use GreenFedora\Console\Output\OutputInterface;
use GreenFedora\Logger\LoggerInterface;
use Gf\Domain\Generator;
use GreenFedora\GetOpt\Parameter;
use GreenFedora\Console\ConsoleApplicationInterface;

/**
 * Generator command.
 * 
 * #[Inject (appPath: appPath)]
 * #[Inject (gfstart: greenfedora_startup_path)]
 * #[Inject (codeDir: cfg|locations.code)]
 * #[Inject (testDir: cfg|locations.test)]
 */
class GeneratorCommand extends Command
{   
    /**
     * Application path.
     * @var string
     */ 
    protected $appPath = null;

    /**
     * Greenfedora startup path.
     * @var string
     */
    protected $gfstart = null;

    /**
     * Code directory.
     * @var string
     */
    protected $codeDir = null;

    /**
     * Test directory.
     * @var string
     */
    protected $testDir = null;

    /**
     * Constructor.
     * 
     * @param   OutputInterface      $output                    Output.
     * @param   LoggerInterface      $logger                    Logger.
     * @param   ConsoleApplicationInterface     $app        Parent application.
     * @param   string|null          $appPath                   Application path.
     * @param   string|null          $gfstart                   Startup path.
     * @param   string|null          $codeDir                   Code directory.
     * @param   string|null          $testDir                   Test directory.
     * @return  void
     */
    public function __construct(
        ?OutputInterface $output = null, 
        LoggerInterface $logger = null, 
        ConsoleApplicationInterface $app = null,        
        ?string $appPath = null, 
        ?string $gfstart = null,
        ?string $codeDir = null,
        ?string $testDir = null
    )
    {
        parent::__construct($output, $logger, $app);
        $this->appPath = $appPath;
        $this->gfstart = $gfstart;
        $this->codeDir = $codeDir;
        $this->testDir = $testDir;
    }

    /**
     * Initialisation.
     * 
     * @return void
     */
    public function init()
    {
        $this->setName('gen')
            ->setDescription('Generate things.')
            ->addPositional('command', "The gen command.", Parameter::HELPIGNORE)
            ->addPositional('component', "Component name.")
            ->addOption('subs', 'Compound names in sub-components.', null, 's')
            ->addOption('noex', 'Do not generate exception files.')
            ->addOption('notest', 'Do not generate test files.')
            ->addOption('help', 'Show this help.', null, 'h');
    }

    /**
     * Execution.
     * 
     * @return  int
     */
    public function execute(): int
    {
        // Get the path.
        $this->debug("Root path: " . $this->gfstart, null, __METHOD__);
        $this->debug("App path: " . $this->appPath, null, __METHOD__);

        // Get the component argument.
        $componentArg = $this->getPositional('component');
        $component = $componentArg->getValue();
        $this->debug("Component: " . $component, null, __METHOD__);

        // Are we generating exceptions?
        $genex = true;
        if ($this->hasOption('noex') and true === $this->getOption('noex')->getValue()) {
            $genex = false;
        }

        // Are we generating tests?
        $tests = true;
        if ($this->hasOption('notests') and true === $this->getOption('notests')->getValue()) {
            $tests = false;
        }

        // Sort out the namespace.
        $mode = 'fw';
        $namespace = 'GreenFedora';
        $targetDir = Path::join($this->gfstart, $this->codeDir);
        $targetPath = Path::join($targetDir, $component);
        $testDir = Path::join($this->gfstart, $this->testDir);
        $testPath = Path::join($testDir, $component);

        if (!file_exists($targetDir)) {
            $this->output->error('This command only works from the framework.');
            return 1;
        }

        if (!file_exists($targetDir)) {
            $this->output->error("Code output directory does not exist: %s.", [$targetDir]);
        }

        if ($tests and !file_exists($testDir)) {
            $this->output->error("Test output directory does not exist: %s.", [$testDir]);
        }

        // Deal with the component's root path.
        $this->debug("Path: " . $targetPath, null, __METHOD__);

        // Evaluate flags.
        $subCompounds = $this->hasOption('subs');

        // Create the generator and call it.
        $generator = new Generator($this->appPath, $targetPath, $testPath, $genex, $tests, 
            $mode, $subCompounds, $this->logger);

        $ret = $generator->doFileGenerations($component, $namespace, 'Interface');

        if (0 === $ret) {
            $msgs = $generator->getInfoMsgs();
            foreach ($msgs as $msg) {
                $this->output->info($msg);
            }
        } else {
            $this->output->error($generator->getLastError());
        }

        $this->output->notice("Successfully created the '%s' component.", [$component]);
        return 0;
    }
}
