<?php

require __DIR__ . '/../vendor/autoload.php';

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
$app->run();
