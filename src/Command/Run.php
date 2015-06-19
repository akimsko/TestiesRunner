<?php
/**
 * This file is part of the TestiesRunner project.
 */

namespace TestiesRunner\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TestiesRunner\Scoper;

/**
 * Class Run
 *
 * @author Bo Thinggaard <akimsko@gmail.com>
 */
class Run extends Command
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('run')
            ->addArgument(
                'test',
                InputArgument::OPTIONAL,
                "Test file to run"
            )
            ->addOption(
                'tests-dir',
                '-d',
                InputOption::VALUE_OPTIONAL,
                "Set tests directory",
                getcwd()
            )
            ->addOption(
                'config',
                '-c',
                InputOption::VALUE_OPTIONAL,
                "Testies config file"
            )
            ->addOption(
                'pattern',
                '-p',
                InputOption::VALUE_OPTIONAL,
                "Test filename pattern (glob)",
                "*Test.php"
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
        $config       = $input->getOption('config');
        $filePattern  = $input->getOption('pattern');
        $testsDir     = $input->getOption('tests-dir')
            ? rtrim($input->getOption('tests-dir'), '/')
            : null
        ;

        if ($test = $input->getArgument('test')) {
            return $this->runSingle($testsDir, $test, $config);
        }

        return $this->runAll($testsDir, $filePattern, $config);
    }

    /**
     * runSingle.
     *
     * @param string $testsDir
     * @param string $test
     * @param string $config
     *
     * @return int
     */
    protected function runSingle($testsDir, $test, $config)
    {
        $this->output->writeln("\n<comment>Running $test</comment>");

        ob_start();
        $exitCode = Scoper::scope("$testsDir/$test");
        $output = ob_get_clean();

        $format = $exitCode ? 'error' : 'info';

        $output->writeln("<$format>$output</$format>");

        return $exitCode;
    }

    /**
     * runAll.
     *
     * @param string $testsDir
     * @param string $filePattern
     * @param string $config
     *
     * @return int
     */
    protected function runAll($testsDir, $filePattern, $config)
    {
        $bin = __DIR__ . '/../../run.php';
        $exitCode = 0;

        foreach (glob("$testsDir/$filePattern") as $testFile) {
            $retVal = 0;

            passthru("$bin run $testFile --test-dir=$testsDir --config={$config}", $retVal);

            $exitCode = $exitCode | $retVal;
        }

        return $exitCode;
    }
}
