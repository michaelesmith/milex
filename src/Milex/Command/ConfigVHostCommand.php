<?php
/**
 * @author msmith
 * @created 5/16/13 4:36 PM
 */

namespace Milex\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigVHostCommand extends Command {
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('config:vhost')
            ->setDescription('Configure the apache vhost for this project')
            ->addArgument('name', InputArgument::REQUIRED, 'Project name')
            ->addArgument('dev-user', InputArgument::OPTIONAL, 'Development username')
            ->addArgument('www-root', InputArgument::OPTIONAL, 'www root dir to use (defaults to current project/web)')
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Overwrite existing config')
            ->addOption('fix-permissions', null, InputOption::VALUE_NONE, 'Fix file and dir permissions')
            ->addOption('no-acl', null, InputOption::VALUE_NONE, 'Do not set ACL dir permissions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        if(file_exists($filename = ('/etc/apache2/sites-available/' . $name)) && !$input->getOption('overwrite')){
            throw new \RuntimeException(sprintf('file "%s" already exists', $filename));
        }

        $www_root = $input->getArgument('www-root') ?: ($this->getService('dir_project') . '/web');

        $this->shell(
            sprintf('echo "Use sf2 %s %s" > %s', $name, $www_root, $filename),
            $output,
            sprintf('Created file "%s"', $filename)
        );

        $this->shell(
            sprintf('a2ensite %s', $name),
            $output,
            sprintf('Enabled "%s" in Apache', $name)
        );

        $this->shell(
            'service apache2 restart',
            $output,
            'Restarted Apache'
        );

        $this->shell(
            sprintf('echo "127.0.0.1\t%s.local" >> /etc/hosts', $name),
            $output,
            sprintf('Added "%s.local" to /etc/hosts', $filename)
        );

        if($input->getOption('fix-permissions')){
            $this->shell(
                'find . -type d -exec chmod 755 {} \;',
                $output,
                'Fixing directory permissions'
            );

            $this->shell(
                'find . -type f -exec chmod 644 {} \;',
                $output,
                'Fixing file permissions'
            );
        }

        $user = $input->getArgument('dev-user');
        $dir = $this->getService('dir_project');

        if(!$input->getOption('no-acl')){
            $this->shell(
                sprintf('setfacl -R -m u:www-data:rwX -m u:%s:rwX %s/app/cache %s/app/logs && setfacl -dR -m u:www-data:rwx -m u:%s:rwx %s/app/cache %s/app/logs', $user, $dir, $dir, $user, $dir, $dir),
                $output,
                sprintf('Configured ACL permissions for "www-data" and "%s" on "app/cache" and "app/logs"', $user)
            );
        }

        $this->shell(
            sprintf('sudo -u%s x-www-browser http://%s.local/app_dev.php', $user, $name),
            $output,
            sprintf('Opening browser to http://%s.local/app_dev.php', $name)
        );

    }

}
