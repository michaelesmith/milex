<?php
/**
 * @author msmith
 * @created 5/16/13 4:34 PM
 */

namespace Milex\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Deploy extends SSHCommand {

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('deploy')
            ->setDescription('Deploy the application')
            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = 'Hello';
        $name = $input->getArgument('name');
        if ($name) {
            $text .= ' '.$name;
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $ssh = $this->ssh;

        $output->writeln($text);
//        $output->writeln(var_export($this->getService('config'), true));
//        $output->writeln($ssh['prod']->getExec()->run('sudo su www-data'));
        $output->writeln($ssh['prod']->getExec()->run('w'));
    }

}
