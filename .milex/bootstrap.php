<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('MyCommands', __DIR__);

$app = new \Milex\Application('Milex', '0.1');

$app['config'] = function(){
    return \Symfony\Component\Yaml\Yaml::parse(
        file_exists(__DIR__ . '/config.yml') ? __DIR__ . '/config.yml' : __DIR__ . '/config.yml.dist'
    );
};

//var_dump(array_merge_recursive(
//    \Symfony\Component\Yaml\Yaml::parse(__DIR__ . '/config.yml.dist'),
//    \Symfony\Component\Yaml\Yaml::parse(__DIR__ . '/config.yml')
//));

$app->command(new \Milex\Command\Deploy());

if(is_dir($dir = __DIR__ . '/MyCommands')) {

    $finder = new \Symfony\Component\Finder\Finder();
    $finder->files()->name('*.php')->in($dir);

    foreach ($finder as $file) {
        $ns = '\\MyCommands';
        if ($relativePath = $file->getRelativePath()) {
            $ns .= '\\'.strtr($relativePath, '/', '\\');
        }
        $r = new \ReflectionClass($ns.'\\'.$file->getBasename('.php'));
        if ($r->isSubclassOf('Milex\\Command\\Command') && !$r->isAbstract()) {
            $app->command($r->newInstance());
        }
    }
}

$app->run();
