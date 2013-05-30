<?php
/**
 * @author msmith
 * @created 5/30/13 3:43 PM
 */

namespace Milex\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SSHCommand extends Command{

    protected $ssh = array();

    protected function configure()
    {
        parent::configure();

        $this
            ->addOption('ssh-identity-file', 'i', InputOption::VALUE_REQUIRED, 'The ssh identity file to use.')
            ->addOption('ssh-user', 'u', InputOption::VALUE_REQUIRED, 'The ssh user to use.')
            ->addOption('ssh-password', 'p', InputOption::VALUE_REQUIRED, 'The ssh password to use.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $container = $this->getContainer();
//        $sshConfig = $container['sshConfig'];
        $sshConfig = array();

        if($input->hasOption('ssh-identity-file')){
            $sshConfig['identityFile'] = $input->getOption('ssh-identity-file');
        }

        if($input->hasOption('ssh-user')){
            $sshConfig['user'] = $input->getOption('ssh-user');
        }

        if($input->hasOption('ssh-password')){
            $sshConfig['password'] = $input->getOption('ssh-password');
        }

        $ssh = array();

        foreach($container['config']['targets'] as $target => $config){
            $configuration = new \Ssh\Configuration($config['host']);
            if(isset($sshConfig['identityFile'])){
                $sshAuth = new \Ssh\Authentication\PublicKeyFile(
                    $sshConfig['user'],
                    $sshConfig['identityFile'] . '.pub',
                    $sshConfig['identityFile'],
                    isset($sshConfig['password']) ? $sshConfig['password'] : null
                );
            }else{
                $sshAuth = new \Ssh\Authentication\Password($sshConfig['user'], $sshConfig['password']);
            }
            $ssh[$target] = new \Ssh\Session($configuration, $sshAuth);
        }

        $this->ssh = $ssh;
    }

}
