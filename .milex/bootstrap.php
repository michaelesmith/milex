<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('MyCommands', __DIR__);

$dir = posix_getcwd();
while($dir != '/'){
    if(file_exists($dir.'/composer.json')){
        break;
    }
    $dir = dirname($dir);
}

$dir = '/' == $dir ? null : $dir;

$app = new \Milex\Application('Milex', '0.1', array(
    'dir_current' => posix_getcwd(),
    'dir_project' => $dir,
    'dir_milex' => __DIR__,
    'dir_user_home' => $_SERVER['HOME'],
));

$app['config'] = function(){
    return \Symfony\Component\Yaml\Yaml::parse(
        file_exists(__DIR__ . '/config.yml') ? __DIR__ . '/config.yml' : __DIR__ . '/config.yml.dist'
    );
};

//var_dump(array_merge_recursive(
//    \Symfony\Component\Yaml\Yaml::parse(__DIR__ . '/config.yml.dist'),
//    \Symfony\Component\Yaml\Yaml::parse(__DIR__ . '/config.yml')
//));

$app->command(new \Milex\Command\ConfigVHostCommand());
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
