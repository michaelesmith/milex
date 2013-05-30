<?php
/**
 * @author msmith
 * @created 5/16/13 4:36 PM
 */

namespace Milex\Command;

use Cilex\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand {

    protected function configure()
    {
        parent::configure();

        $this
            ->addOption('target', 't', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The host from config to use')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
    }

}
