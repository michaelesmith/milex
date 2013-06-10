<?php
/**
 * @author msmith
 * @created 6/10/13 2:48 PM
 */

namespace MyCommands;

use Milex\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MyCommand extends Command {

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('my-command')
            ->setDescription('My new Command')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

}
