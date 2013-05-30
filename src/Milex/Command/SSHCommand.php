<?php
/**
 * @author msmith
 * @created 5/30/13 3:43 PM
 */

namespace Milex\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\InvalidArgumentException;

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
                if(!$sshConfig['user'] || !$sshConfig['identityFile']){
                    throw new InvalidArgumentException('Both ssh-user and ssh-identity-file must be given to use public key authentication');
                }
                $sshAuth = new \Ssh\Authentication\PublicKeyFile(
                    $sshConfig['user'],
                    $sshConfig['identityFile'] . '.pub',
                    $sshConfig['identityFile'],
                    isset($sshConfig['password']) ? $sshConfig['password'] : null
                );
            }else{
                if(!$sshConfig['user'] || !$sshConfig['password']){
                    throw new InvalidArgumentException('Both ssh-user and ssh-password must be given to use password authentication');
                }
                $sshAuth = new \Ssh\Authentication\Password($sshConfig['user'], $sshConfig['password']);
            }
            $ssh[$target] = new \Ssh\Session($configuration, $sshAuth);
        }

        $this->ssh = $ssh;
    }

}
