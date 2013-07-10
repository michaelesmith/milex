<?php
/**
 * @author msmith
 * @created 5/16/13 4:36 PM
 */

namespace Milex\Command;

use Cilex\Command\Command as BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Command extends BaseCommand {

    protected function shell($cmd, OutputInterface $output, $success = null)
    {
        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $output->writeln(sprintf('<comment>%s</comment>', $cmd));
        }

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $output->writeln(sprintf('<question>%s</question>', $process->getOutput()));
        }

        if($success){
            $output->writeln($success);
        }
    }
}
