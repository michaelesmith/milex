<?php
/**
 * @author msmith
 * @created 5/16/13 4:36 PM
 */

namespace Milex\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TargetCommand extends Command {

    protected $targets = array();

    protected function configure()
    {
        parent::configure();

        $this
            ->addOption('target', 't', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The host from config to use')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $configTargets = $container['config']['targets'];

        if(count($input->getOption('target'))){
            $targets = $input->getOption('target');
        }else{
            $output->writeln('No targets given, using all configured targets');
            $targets = array_keys($configTargets);
        }

        foreach($targets as $target){
            if(!isset($configTargets[$target])){
                throw new \InvalidArgumentException(sprintf('Target "%s" is not configured', $target));
            }

            $this->targets[$target] = $configTargets[$target];
            $output->writeln(sprintf('Using target %s: %s', $target, $configTargets[$target]['host']));
        }

        parent::initialize($input, $output);
    }

}
